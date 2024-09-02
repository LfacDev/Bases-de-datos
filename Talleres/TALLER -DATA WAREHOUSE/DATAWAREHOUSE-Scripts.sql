USE AdventureWorks2019
--que estan haciendo en cada paso 

 --tomamos todas las ventas, las consolidamos y creamos una nueva tabla no normalizada, despues de transformar redujimos la cantidad de registros y se consolido el total de ordenes  

 --extraemos la informacion del encabezado del pedido con los detalles del pedido 
 --creamos una tabla temporal no normalizada (motraba datos repetidos)
 --consolidamos a nivel de cliente y tenemos un solo registro por cliente 
 --resumimos el total ,el numero de pedido y la ultima compra
 --de la tabla transform nos pasamos a la tabla destino

SELECT 

    s.SalesOrderID,

    h.OrderDate,

    c.CustomerID,

    p.ProductID,

    p.Name AS ProductName,

    s.OrderQty,

    s.LineTotal

INTO #SalesData

FROM 

    AdventureWorks2019.Sales.SalesOrderDetail s

    JOIN AdventureWorks2019.Sales.SalesOrderHeader h ON s.SalesOrderID = h.SalesOrderID

    JOIN AdventureWorks2019.Sales.Customer c ON h.CustomerID = c.CustomerID

    JOIN AdventureWorks2019.Production.Product p ON s.ProductID = p.ProductID;
 
 use tempdb

 select * from dbo.#SalesData where dbo.#SalesData.SalesOrderID = 43659

 ---Transfromar datos

 SELECT 
    CustomerID,
	sum(LineTotal) AS TotalSpent,
	COUNT(DISTINCT SalesOrderID) AS OrderCount,
	MAX(OrderDate) AS LastOrderDate
	into #TransformedSalesData
	from 
	#SalesData
	group by 
	CustomerID;

use tempdb

 select * from dbo.#TransformedSalesData where customerID = 29825

 USE AdventureWorks2019

 ------------------------------------------------------------

 --crea tabla en el data werehouse

 USE AdventureWorksDW2019
-- Crear la tabla destino si no existe
IF NOT EXISTS (SELECT * FROM AdventureWorksDW2019.INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'CustomerSalesSummary')
BEGIN
    CREATE TABLE AdventureWorksDW2019.dbo.CustomerSalesSummary (
        CustomerID INT PRIMARY KEY,
        TotalSpent MONEY,
        OrderCount INT,
        LastOrderDate DATETIME
    );
END
 
 
USE AdventureWorksDW2019
-- Cargar los datos transformados en la tabla destino
INSERT INTO AdventureWorksDW2019.dbo.CustomerSalesSummary (CustomerID, TotalSpent, OrderCount, LastOrderDate)
SELECT 
    CustomerID,
    TotalSpent,
    OrderCount,
    LastOrderDate
FROM 
    #TransformedSalesData;

	------------------------------- OTRO EJEMPLO -------------------------------------------------

--Extraemos toda la información relevante sobre pedidos, productos, categorías, empleados y detalles del pedido.
--Creamos una tabla temporal llamada #TempSalesData para almacenar estos datos sin consolidar.
SELECT
    p.ProductID,
    p.Name AS ProductName,
    pc.Name AS CategoryName,
    e.BusinessEntityID AS EmployeeID,
    per.FirstName + ' ' + per.LastName AS EmployeeName,
    soh.SalesOrderID,
    soh.OrderDate,
    sod.OrderQty,
    sod.UnitPrice,
    sod.LineTotal
INTO #TempSalesData
FROM
    Sales.SalesOrderHeader AS soh
    INNER JOIN Sales.SalesOrderDetail AS sod ON soh.SalesOrderID = sod.SalesOrderID
    INNER JOIN Production.Product AS p ON sod.ProductID = p.ProductID
    INNER JOIN Production.ProductCategory AS pc ON p.ProductSubcategoryID = pc.ProductCategoryID
    INNER JOIN Sales.SalesPerson AS sp ON soh.SalesPersonID = sp.BusinessEntityID
    INNER JOIN HumanResources.Employee AS e ON sp.BusinessEntityID = e.BusinessEntityID
    INNER JOIN Person.Person AS per ON e.BusinessEntityID = per.BusinessEntityID;

--Agrupamos los datos por empleado y categoría.
--Calculamos el número total de pedidos, la suma de ventas y la fecha del último pedido para cada combinación de empleado y categoría.
--Creamos otra tabla temporal, #ConsolidatedSalesData, con estos datos resumidos.
SELECT
	EmployeeID,
    EmployeeName,
    CategoryName,
    COUNT(DISTINCT SalesOrderID) AS NumberOfOrders,
    SUM(LineTotal) AS TotalSales,
    MAX(OrderDate) AS LastPurchaseDate
INTO #ConsolidatedSalesData
FROM
    #TempSalesData
GROUP BY
    EmployeeID,
    EmployeeName,
    CategoryName;

--Definimos la estructura de una tabla física llamada TablaDestino para almacenar los datos consolidados de forma permanente.
CREATE TABLE TablaDestino (
    EmployeeID INT,
    EmployeeName NVARCHAR(100),
    CategoryName NVARCHAR(100),
    NumberOfOrders INT,
    TotalSales DECIMAL(18, 2),
    LastPurchaseDate DATE
);

--Transferimos los datos consolidados desde la tabla temporal #ConsolidatedSalesData a la tabla física TablaDestino.

	INSERT INTO TablaDestino (EmployeeID, EmployeeName, CategoryName, NumberOfOrders, TotalSales, LastPurchaseDate)
SELECT
    EmployeeID,
    EmployeeName,
    CategoryName,
    NumberOfOrders,
    TotalSales,
    LastPurchaseDate
FROM
    #ConsolidatedSalesData;


	-- Añadir la columna ProductName
ALTER TABLE TablaDestino
ADD ProductName NVARCHAR(100);

-- Insertar datos actualizados en la tabla destino
INSERT INTO TablaDestino (EmployeeID, EmployeeName, CategoryName, ProductName, NumberOfOrders, TotalSales, LastPurchaseDate)
SELECT
    e.BusinessEntityID AS EmployeeID,
    per.FirstName + ' ' + per.LastName AS EmployeeName,
    pc.Name AS CategoryName,
    p.Name AS ProductName,
    COUNT(DISTINCT soh.SalesOrderID) AS NumberOfOrders,
    SUM(sod.LineTotal) AS TotalSales,
    MAX(soh.OrderDate) AS LastPurchaseDate
FROM
    Sales.SalesOrderHeader AS soh
    INNER JOIN Sales.SalesOrderDetail AS sod ON soh.SalesOrderID = sod.SalesOrderID
    INNER JOIN Production.Product AS p ON sod.ProductID = p.ProductID
    INNER JOIN Production.ProductCategory AS pc ON p.ProductSubcategoryID = pc.ProductCategoryID
    INNER JOIN Sales.SalesPerson AS sp ON soh.SalesPersonID = sp.BusinessEntityID
    INNER JOIN HumanResources.Employee AS e ON sp.BusinessEntityID = e.BusinessEntityID
    INNER JOIN Person.Person AS per ON e.BusinessEntityID = per.BusinessEntityID
GROUP BY
    e.BusinessEntityID,
    per.FirstName + ' ' + per.LastName,
    pc.Name,
    p.Name;
