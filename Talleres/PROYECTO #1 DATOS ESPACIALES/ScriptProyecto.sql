CREATE DATABASE IF NOT EXISTS DatosEspaciales;
use DatosEspaciales;
create table Clientes (
 Cedula int primary key,
 Nombre varchar(50),
 Apellidos varchar(50),
 Direccion varchar(100),
 Ubi_Latitud decimal (18,6),
 Ubi_Longitud decimal (18,6)
 );
 
 
 SET @lat = 4.632603;
 SET @lnt = -74.080850;
 SELECT Cedula, Nombre, Apellidos, Direccion, Ubi_Latitud, Ubi_Longitud, 
        ST_Distance_Sphere(
        POINT(Ubi_Latitud, Ubi_Longitud), 
        POINT(@lat, @lnt) 
        ) AS Distancia,
        ROW_NUMBER() OVER (ORDER BY ST_Distance_Sphere(
        POINT(Ubi_Latitud, Ubi_Longitud), 
        POINT(@lat, @lnt)) asc) AS Ranking
        from Clientes 
        ORDER BY Distancia ASC 