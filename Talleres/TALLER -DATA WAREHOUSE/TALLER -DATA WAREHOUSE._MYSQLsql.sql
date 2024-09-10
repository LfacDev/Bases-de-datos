CREATE DATABASE IF NOT EXISTS NorthwindDW;
USE NorthwindDW;
 -- Creamos la base con las tablas de dimensiones a analizar
CREATE TABLE DimFecha (
    FechaKey INT PRIMARY KEY,
    Fecha DATE NOT NULL,
    Año INT NOT NULL,
    Trimestre INT NOT NULL,
    Mes INT NOT NULL,
    Dia INT NOT NULL,
    NombreMes VARCHAR(20),
    NombreDia VARCHAR(20),
    EsFinDeSemana BOOLEAN
);
 
 
CREATE TABLE DimProducto (
    ProductoKey INT PRIMARY KEY AUTO_INCREMENT,
    ProductoID INT NOT NULL,
    NombreProducto VARCHAR(100) NOT NULL,
    Categoría VARCHAR(100),
    Proveedor VARCHAR(100),
    PrecioUnitario DECIMAL(10, 2)
);
 
CREATE TABLE DimCliente (
    ClienteKey INT PRIMARY KEY AUTO_INCREMENT,
    ClienteID VARCHAR(5) NOT NULL,
    NombreCliente VARCHAR(100),
    Contacto VARCHAR(100),
    Ciudad VARCHAR(50),
    País VARCHAR(50)
);
 
CREATE TABLE DimEmpleado (
    EmpleadoKey INT PRIMARY KEY AUTO_INCREMENT,
    EmpleadoID INT NOT NULL,
    NombreEmpleado VARCHAR(100),
    Título VARCHAR(50),
    Ciudad VARCHAR(50),
    País VARCHAR(50)
);
 
 
CREATE TABLE FactVentas (
    VentaKey INT PRIMARY KEY AUTO_INCREMENT,
    FechaKey INT NOT NULL,
    ProductoKey INT NOT NULL,
    ClienteKey INT NOT NULL,
    EmpleadoKey INT NOT NULL,
    Cantidad INT NOT NULL,
    PrecioUnitario DECIMAL(10, 2) NOT NULL,
    MontoTotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (FechaKey) REFERENCES DimFecha(FechaKey),
    FOREIGN KEY (ProductoKey) REFERENCES DimProducto(ProductoKey),
    FOREIGN KEY (ClienteKey) REFERENCES DimCliente(ClienteKey),
    FOREIGN KEY (EmpleadoKey) REFERENCES DimEmpleado(EmpleadoKey)
);

-- DIMENSION PRODUCTO
INSERT INTO DimProducto (ProductoID, NombreProducto, Categoría, Proveedor, PrecioUnitario)
SELECT
    ProductID AS ProductoID,
    ProductName AS NombreProducto,
    c.CategoryName AS Categoría,
    s.SupplierName AS Proveedor,
    Price AS PrecioUnitario
FROM
    Northwind.Products p
JOIN
    Northwind.Categories c ON p.CategoryID = c.CategoryID
JOIN
    Northwind.Suppliers s ON p.SupplierID = s.SupplierID;

-- DIMENSION FECHA
INSERT INTO DimFecha (FechaKey, Fecha, Año, Trimestre, Mes, Dia, NombreMes, NombreDia, EsFinDeSemana)
SELECT DISTINCT 
    DATE_FORMAT(OrderDate, '%Y%m%d') AS FechaKey,
    OrderDate AS Fecha,
    YEAR(OrderDate) AS Año,
    QUARTER(OrderDate) AS Trimestre,
    MONTH(OrderDate) AS Mes,
    DAY(OrderDate) AS Dia,
    MONTHNAME(OrderDate) AS NombreMes,
    DAYNAME(OrderDate) AS NombreDia,
    IF(DAYOFWEEK(OrderDate) IN (1, 7), TRUE, FALSE) AS EsFinDeSemana
FROM 
    Northwind.Orders;
    
-- DIMENSION CLIENTE
INSERT INTO DimCliente (ClienteID, NombreCliente, Contacto, Ciudad, País)
SELECT 
    CustomerID AS ClienteID,
    CustomerName AS NombreCliente,
    ContactName AS Contacto,
    City AS Ciudad,
    Country AS País
FROM 
    Northwind.Customers;
 
 -- DIMENSION EMPLEADO
INSERT INTO DimEmpleado (EmpleadoID, NombreEmpleado)
SELECT 
    EmployeeID AS EmpleadoID,
    CONCAT(FirstName, ' ', LastName) AS NombreEmpleado
FROM 
    Northwind.Employees;
 
 -- VENTAS
INSERT INTO FactVentas (FechaKey, ProductoKey, ClienteKey, EmpleadoKey, Cantidad, PrecioUnitario, MontoTotal)
SELECT 
    DATE_FORMAT(o.OrderDate, '%Y%m%d') AS FechaKey,
    p.ProductID AS ProductoKey,
    c.CustomerID AS ClienteKey,
    e.EmployeeID AS EmpleadoKey,
    od.Quantity AS Cantidad,
    p.Price AS PrecioUnitario,
    od.Quantity * p.Price AS MontoTotal
FROM 
    Northwind.Orders o
JOIN 
    Northwind.Orderdetails od ON o.OrderID = od.OrderID
JOIN 
    Northwind.Products p ON od.ProductID = p.ProductID
JOIN 
    Northwind.Customers c ON o.CustomerID = c.CustomerID
JOIN 
    Northwind.Employees e ON o.EmployeeID = e.EmployeeID;

-- Crear Vistas     
CREATE VIEW VistaVentasPorProducto AS
SELECT 
    p.NombreProducto,
    SUM(f.Cantidad) AS TotalCantidad,
    SUM(f.MontoTotal) AS TotalVentas
FROM 
    FactVentas f
JOIN 
    DimProducto p ON f.ProductoKey = p.ProductoKey
GROUP BY 
    p.NombreProducto;