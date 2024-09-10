-- Usamos primero la base principal
USE AdventureWorks2019
-- Select * from Sales.SalesOrderDetail where CustomerID=29825
-- Select * from Sales.SalesOrderDetail
-- Select * from Sales.SalesOrderHeader
-- Select * from sales.customer where PersonID = 609
-- Select * from Person.Person where BusinessEntityID = 430
-- select * from Production.Product 
-- select * from Production.ProductCategory 
-- select * from Production.ProductSubcategory
-- Select * from sales.SalesPerson where BusinessEntityID = 276
-- Select * from HumanResources.Employee where BusinessEntityID = 1
-- Select * from Person.Person where BusinessEntityID = 276
-- ProductSubcategoryID -> producto
-- HeaderSales 279 -> SalesPersonID -> 

-- 1. Extraer: De la base AdventureWorks2019, extraemos datos de 4 tablas diferentes para unirlos y resumir la informacion en una sola tabla temporal no relacional
SELECT 
    s.SalesOrderID,
    h.OrderDate,
	h.SalesPersonID,
    c.CustomerID,
	c.PersonID,
	per.BusinessEntityID AS IDPerson,
	Em.BusinessEntityID AS EmpleadoID,
	CONCAT(per.FirstName , per.LastName) AS PersonName,
	sub.ProductSubcategoryID AS IDSubCategory,
	sub.ProductCategoryID AS FKCategory,
	cat.ProductCategoryID AS IDCategory,
	cat.Name AS CategoryName,
	sl.BusinessEntityID AS PersonSalesID,
    p.ProductID,
    p.Name AS ProductName,
    s.OrderQty,
    s.LineTotal

INTO dbo.#SalesData

FROM 
    AdventureWorks2019.Sales.SalesOrderDetail s
    JOIN AdventureWorks2019.Sales.SalesOrderHeader h ON s.SalesOrderID = h.SalesOrderID
    JOIN AdventureWorks2019.Sales.Customer c ON h.CustomerID = c.CustomerID
    JOIN AdventureWorks2019.Production.Product p ON s.ProductID = p.ProductID
	JOIN AdventureWorks2019.Production.ProductSubcategory sub ON sub.ProductSubcategoryID= p.ProductSubcategoryID
	JOIN AdventureWorks2019.Production.ProductCategory cat ON cat.ProductCategoryID = sub.ProductCategoryID
	JOIN AdventureWorks2019.Sales.SalesPerson sl ON sl.BusinessEntityID = h.SalesPersonID
	JOIN AdventureWorks2019.HumanResources.Employee Em ON Em.BusinessEntityID=sl.BusinessEntityID
	JOIN AdventureWorks2019.Person.Person per ON per.BusinessEntityID = c.PersonID
	

	
drop table dbo.#SalesData
select * from dbo.#SalesData


-- usamos la base temporal
Use tempdb;
-- Seleccionamos la tabla temporal creada y consultamos por el numero de orden que nos interesa
Select * from dbo.#SalesData where SalesOrderId=43659;

-- Consultamos por el numero de cliente deseado
Select COUNT(DISTINCT SalesOrderID) from dbo.#SalesData where CustomerID=29825;


-- 2. Transformar: De la tabla temporal creada en el paso uno, tomamos los datos para obtener el resumen de ventas por cliente y la informacion mas organizada

cliente, empleado vista => dimension

-- Transformar informacion por cliente 
SELECT 
	CustomerID,
	PersonName,
	SUM(LineTotal) AS TotalSpent,
	COUNT(DISTINCT SalesOrderID) AS OrderCount,
	MAX(OrderDate) AS LastOrderDate
INTO #TransformedSalesDataCustomer
FROM 
	#SalesData
GROUP BY
	CustomerID, PersonName;


Select * from dbo.#TransformedSalesDataCustomer

-- Transformar informacion por producto
SELECT 
	ProductID,
	ProductName,
	SUM(OrderQty) AS Cantidad,
	SUM(LineTotal) AS TotalSpent,
	COUNT(DISTINCT SalesOrderID) AS OrderCount,
	MAX(OrderDate) AS LastOrderDate
INTO #TransformedSalesDataProduct
FROM 
	#SalesData
GROUP BY
	ProductID, ProductName;

Select * from dbo.#TransformedSalesDataProduct;
Select * from dbo.#SalesData;


-- Transformar informacion por Categoria
SELECT 
	IDCategory,
	CategoryName,
	SUM(OrderQty) AS Cantidad,
	SUM(LineTotal) AS TotalSpent,
	COUNT(DISTINCT SalesOrderID) AS OrderCount,
	MAX(OrderDate) AS LastOrderDate
INTO #TransformedSalesDataCategory
FROM 
	#SalesData
GROUP BY
	IDCategory, CategoryName;


Select * from dbo.#TransformedSalesDataCategory;
Select * from dbo.#SalesData;


-- Transformar informacion por Empleado
SELECT 
	EmpleadoID,
	MAX(per.BusinessEntityID) AS PersonID, -- Relacionamos el empleado con la persona
	CONCAT(per.FirstName, ' ', per.LastName) AS EmployeeName, -- Concatenamos el nombre y apellido del empleado
	SUM(OrderQty) AS Cantidad,
	SUM(LineTotal) AS TotalSpent,
	COUNT(DISTINCT SalesOrderID) AS OrderCount,
	MAX(OrderDate) AS LastOrderDate
INTO #TransformedSalesDataEmployee
FROM 
	#SalesData
JOIN AdventureWorks2019.Person.Person per ON per.BusinessEntityID = EmpleadoID -- Unimos con la tabla de personas
GROUP BY
	EmpleadoID, per.FirstName, per.LastName; -- Agrupamos también por el nombre del empleado




Select * from dbo.#TransformedSalesDataEmployee;
drop table dbo.#TransformedSalesDataEmployee;

-- Consultamos por el numero de cliente que nos interesa
Select * from dbo.#TransformedSalesDataCustomer where CustomerID=30116;


-- 3. Cargar: En la base de datos "ALMACEN" (la base que va a contener todos los resumenes de datos), creamos un procedimiento almacenado que nos permita generar una tabla para guardar los datos trasnformados, con los que luego generaremos un informe
-- CREAR TABLA En la base Winehouse

USE AdventureWorksDW2019;

Select * from dbo.#TransformedSalesDataCustomer;
Select * from dbo.#TransformedSalesDataProduct;
Select * from dbo.#TransformedSalesDataCategory;
Select * from dbo.#TransformedSalesDataEmployee;

-- Crear la tabla destino si no existe


-- Para Cliente
IF NOT EXISTS (SELECT * FROM AdventureWorksDW2019.INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'CustomerSalesSummary')
BEGIN
    CREATE TABLE AdventureWorksDW2019.dbo.CustomerSalesSummary (
        CustomerID INT PRIMARY KEY,
		PersonName Varchar(50),
        TotalSpent MONEY,
        OrderCount INT,
        LastOrderDate DATETIME
    );
END
 

 -- Para Producto
IF NOT EXISTS (SELECT * FROM AdventureWorksDW2019.INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ProductSalesSummary')
BEGIN
    CREATE TABLE AdventureWorksDW2019.dbo.ProductSalesSummary (
        ProductID INT PRIMARY KEY,
		ProductName Varchar(50),
		Cantidad INT,
        TotalSpent MONEY,
        OrderCount INT,
		LastOrderDate DATETIME
    );
END


 -- Para Category
IF NOT EXISTS (SELECT * FROM AdventureWorksDW2019.INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'CatogorySalesSummary')
BEGIN
    CREATE TABLE AdventureWorksDW2019.dbo.CatogorySalesSummary (
        IDCategory INT PRIMARY KEY,
		CategoryName Varchar(50),
		Cantidad INT,
        TotalSpent MONEY,
        OrderCount INT,
		LastOrderDate DATETIME
    );
END


 -- Para Employee
IF NOT EXISTS (SELECT * FROM AdventureWorksDW2019.INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'EmployeeSalesSummary')
BEGIN
    CREATE TABLE AdventureWorksDW2019.dbo.EmployeeSalesSummary (
        EmpleadoID INT PRIMARY KEY,
		EmployeeName VARCHAR(50),
		Cantidad INT,
        TotalSpent MONEY,
        OrderCount INT,
		LastOrderDate DATETIME
    );
END

drop table EmployeeSalesSummary
 
USE AdventureWorksDW2019
-- Cargar los datos transformados en la tabla destino

-- Para Cliente
INSERT INTO AdventureWorksDW2019.dbo.CustomerSalesSummary (CustomerID, PersonName, TotalSpent, OrderCount, LastOrderDate)
SELECT 
    CustomerID,
	PersonName,
    TotalSpent,
    OrderCount,
    LastOrderDate
FROM 
    #TransformedSalesDataCustomer;

	Select * from CustomerSalesSummary


-- Para Producto
INSERT INTO AdventureWorksDW2019.dbo.ProductSalesSummary (ProductID, ProductName, Cantidad, TotalSpent, OrderCount, LastOrderDate)
SELECT 
	ProductID, 
	ProductName, 
	Cantidad, 
	TotalSpent, 
	OrderCount,
	LastOrderDate
FROM 
    #TransformedSalesDataProduct;

	Select * from ProductSalesSummary

-- Para Categoria
INSERT INTO AdventureWorksDW2019.dbo.CatogorySalesSummary (IDCategory, CategoryName, Cantidad, TotalSpent, OrderCount, LastOrderDate)
SELECT 
	IDCategory, 
	CategoryName, 
	Cantidad, 
	TotalSpent, 
	OrderCount,
	LastOrderDate
FROM 
    #TransformedSalesDataCategory;

	Select * from CatogorySalesSummary

-- Para Employee
INSERT INTO AdventureWorksDW2019.dbo.EmployeeSalesSummary (EmpleadoID, EmployeeName, Cantidad, TotalSpent, OrderCount, LastOrderDate)
SELECT 
	EmpleadoID,EmployeeName, Cantidad, TotalSpent, OrderCount, LastOrderDate
FROM 
    #TransformedSalesDataEmployee;

	Select * from EmployeeSalesSummary

drop table dbo.#TransformedSalesDataCustomer;
drop table dbo.#TransformedSalesDataProduct;
drop table dbo.#TransformedSalesDataCategory;
drop table dbo.#TransformedSalesDataEmployee;


drop table CustomerSalesSummary
drop table ProductSalesSummary
drop table CatogorySalesSummary
drop table EmployeeSalesSummary


CREATE VIEW RankingClientes AS
SELECT 
    PersonName,
    TotalSpent,
    ROW_NUMBER() OVER (ORDER BY TotalSpent DESC) AS Ranking
FROM 
    CustomerSalesSummary
GROUP BY 
   PersonName, TotalSpent


Select * From RankingClientes


