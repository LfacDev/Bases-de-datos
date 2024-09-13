CREATE DATABASE IF NOT EXISTS SakilaDW;
USE SakilaDW;

CREATE TABLE DimFecha (
FechaKey INT PRIMARY KEY,
Fecha DATE NOT NULL,
Dia INT NOT NULL,
Mes INT NOT NULL,
Año INT NOT NULL,
NombreMes VARCHAR(20),
NombreDia VARCHAR(20),
EsFinDeSemana BOOLEAN
);


CREATE TABLE DimPelicula(
PeliculaKey INT PRIMARY KEY AUTO_INCREMENT,
PeliculaID INT NOT NULL,
NombrePelicula VARCHAR(100),
Lenguaje VARCHAR(100),
Descripcion VARCHAR(150),
Categoria VARCHAR(100),
Rating Double
);

CREATE TABLE DimVendedor(
VendedorKey INT PRIMARY KEY AUTO_INCREMENT,
VendedorID INT NOT NULL,
NombreVendedor VARCHAR(100),
ApellidoVendedor VARCHAR(100),
Ciudad VARCHAR(100),
Pais VARCHAR(100)
);

CREATE TABLE DimCliente(
ClienteKey INT PRIMARY KEY AUTO_INCREMENT,
ClienteID INT NOT NULL,
NombreCliente VARCHAR(100),
ApellidoCliente VARCHAR(100),
Ciudad VARCHAR(100),
Pais VARCHAR(100)
);


CREATE TABLE FactAlquiler(
AlquilerKey INT PRIMARY KEY AUTO_INCREMENT,
FechaKey INT NOT NULL, 
PeliculaKey INT NOT NULL,
VendedorKey INT NOT NULL,
ClienteKey INT NOT NULL,
Cantidad INT NOT NULL,
MontoTotal DOUBLE,
FOREIGN KEY (FechaKey) REFERENCES DimFecha(FechaKey),
FOREIGN KEY (PeliculaKey) REFERENCES DimPelicula(PeliculaKey),
FOREIGN KEY (ClienteKey) REFERENCES DimCliente(ClienteKey),
FOREIGN KEY (VendedorKey) REFERENCES DimVendedor(VendedorKey)
);


-- DIMENSION CLIENTES
INSERT INTO DimCliente (ClienteID, NombreCliente, ApellidoCliente, Ciudad, Pais)
SELECT 
customer_id AS ClienteID,
first_name AS NombreCliente,
last_name AS ApellidoCliente,
ci.city AS Ciudad,
co.country AS Pais
FROM sakila.customer cu
JOIN sakila.address a on cu.address_id = a.address_id
JOIN sakila.city ci on a.city_id = ci.city_id
JOIN sakila.country co on ci.country_id = co.country_id; 

-- DIMENSION VENDEDOR
INSERT INTO DimVendedor (VendedorID, NombreVendedor, ApellidoVendedor, Ciudad, Pais)
SELECT 
staff_id AS VendedorID,
first_name AS NombreVendedor,
last_name AS ApellidoVendedor,
ci.city AS Ciudad,
co.country AS Pais
FROM sakila.staff s
JOIN sakila.address a on s.address_id = a.address_id
JOIN sakila.city ci on a.city_id = ci.city_id
JOIN sakila.country co on ci.country_id = co.country_id;

-- DIMENSION PELICULA
INSERT INTO DimPelicula(PeliculaID, NombrePelicula, Lenguaje, Descripcion, Categoria, Rating)
SELECT 
  f.film_id AS PeliculaID,
  f.title AS NombrePelicula,
  la.name AS Lenguaje,
  f.description AS Descripcion,
  (SELECT ca.name 
   FROM sakila.film_category fc 
   JOIN sakila.category ca ON fc.category_id = ca.category_id
   WHERE fc.film_id = f.film_id
   LIMIT 1) AS Categoria,
  f.rating AS Rating
FROM sakila.film f
JOIN sakila.language la ON f.language_id = la.language_id;

-- DIMENSION FECHA 
INSERT IGNORE INTO DimFecha (FechaKey, Fecha, Dia, Mes, Año, NombreMes, NombreDia, EsFinDeSemana)
SELECT 
    DATE_FORMAT(rental_date, '%Y%m%d') AS FechaKey,
    DATE(rental_date) AS Fecha,
    DAY(rental_date) AS Dia,
    MONTH(rental_date) AS Mes,
    YEAR(rental_date) AS Año,
    MONTHNAME(rental_date) AS NombreMes,
    DAYNAME(rental_date) AS NombreDia,
    IF(DAYOFWEEK(rental_date) IN (1, 7), TRUE, FALSE) AS EsFinDeSemana
FROM 
    sakila.rental
GROUP BY 
    FechaKey, Fecha, Dia, Mes, Año, NombreMes, NombreDia, EsFinDeSemana;
    
    
-- DIMENSION FACTALQUILER
INSERT INTO FactAlquiler(FechaKey, PeliculaKey, VendedorKey, ClienteKey, Cantidad, MontoTotal)
SELECT 
DATE_FORMAT(r.rental_date, '%Y%m%d') AS FechaKey,   
f.film_id AS PeliculaKey,
s.staff_id AS VendedorKey,
c.customer_id AS ClienteKey,
COUNT(r.rental_id) AS Cantidad,
SUM(p.amount) AS MontoTotal
FROM sakila.rental r
JOIN sakila.inventory i ON r.inventory_id = i.inventory_id
JOIN sakila.film f ON i.film_id = f.film_id
JOIN sakila.customer c ON r.customer_id = c.customer_id
JOIN sakila.staff s ON r.staff_id = s.staff_id
JOIN sakila.payment p ON r.rental_id = p.rental_id
GROUP BY FechaKey, PeliculaKey, VendedorKey, ClienteKey;
 
    
    
-- VISTA VENTA POR PELICULA
CREATE VIEW VistaVentasPorPelicula AS
SELECT 
    p.NombrePelicula,
    SUM(f.Cantidad) AS TotalCantidad,
    SUM(f.MontoTotal) AS TotalVentas
FROM 
    FactAlquiler f
JOIN 
    DimPelicula p ON f.PeliculaKey = p.PeliculaKey
GROUP BY 
    p.NombrePelicula;
    
    -- VISTA VENTA POR CLIENTE
CREATE VIEW VistaVentasPorCliente AS
SELECT 
    c.NombreCliente,
    SUM(f.Cantidad) AS TotalCantidad,
    SUM(f.MontoTotal) AS TotalVentas
FROM 
    FactAlquiler f
JOIN 
    DimCliente c ON f.ClienteKey = c.ClienteKey
GROUP BY 
    c.NombreCliente;
    
        -- VISTA VENTA POR VENDEDOR
CREATE VIEW VistaVentasPorVendedor AS
SELECT 
    v.NombreVendedor,
    SUM(f.Cantidad) AS TotalCantidad,
    SUM(f.MontoTotal) AS TotalVentas
FROM 
    FactAlquiler f
JOIN 
    DimVendedor v ON f.VendedorKey = v.VendedorKey
GROUP BY 
    v.NombreVendedor;







 

