<!-- Aqui realizaremos los querys para armar el crud -->
<?php

require_once '../DTO/ClienteDTO.php';

class ClienteDAO {
    private $dbConexion;

    public function __construct($dbConexion) {
        $this->dbConexion = $dbConexion;
    }

    public function mostrarDistancia($latitud , $longitud){
        $lat= $latitud;
        $lnt= $longitud;
        $consulta = $this->dbConexion->prepare('SELECT Cedula, Nombre, Apellidos, Direccion, Ubi_Latitud, Ubi_Longitud, 
        ST_Distance_Sphere(
        POINT(Ubi_Latitud, Ubi_Longitud), 
        POINT(:latitud, :longitud) 
        ) AS Distancia,
        ROW_NUMBER() OVER (ORDER BY ST_Distance_Sphere(
        POINT(Ubi_Latitud, Ubi_Longitud), 
        POINT(:latitud, :longitud)) asc) AS Ranking
        from Clientes');
        $consulta->bindParam(':latitud', $lat);
        $consulta->bindParam(':longitud', $lnt);
        $consulta->execute();
        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $registros;
    }


    public function mostrarClientes(){
        $consulta = $this->dbConexion->prepare('SELECT * FROM Clientes');
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public function mostrarClienteCedula($Cedula){

        $consulta = $this->dbConexion->prepare('SELECT * FROM Clientes WHERE Cedula = :cedula');
        $consulta->bindParam(':cedula', $Cedula);
        $consulta->execute();
        $registro = $consulta->fetch();

        if($registro === false){
            
            header("Location: ../vistas/indexCliente.php");
            exit(); 
        }

        return $registro;
    }

    public function CrearCliente(ClienteDTO $cliente){

        $consulta = $this->dbConexion->prepare('INSERT INTO Clientes (Cedula, Nombre, Apellidos, Direccion, Ubi_Latitud, Ubi_Longitud) VALUES (:cedula, :nombre, :apellidos, :direccion, :ubi_Latitud, :ubi_Longitud)');
        $consulta->bindParam(':cedula', $cliente->getCedula());
        $consulta->bindParam(':nombre', $cliente->getNombres());
        $consulta->bindParam(':apellidos', $cliente->getApellidos());
        $consulta->bindParam(':direccion', $cliente->getDireccion());
        $consulta->bindParam(':ubi_Latitud', $cliente->getUbi_Latitud());
        $consulta->bindParam(':ubi_Longitud', $cliente->getUbi_Longitud());
        $consulta->execute();
        
        header("Location:../vistas/indexCliente.php");
        exit();
    }

    public function editarCliente(ClienteDTO $cliente){

        
        $consulta = $this->dbConexion->prepare('UPDATE Clientes SET Cedula =:cedula, Nombre= :nombre, Apellidos = :apellidos, Direccion = :direccion, Ubi_Latitud = :latitud, Ubi_Longitud = :longitud WHERE Cedula = :cedula');
        $consulta->bindParam(':cedula', $cliente->getCedula());
        $consulta->bindParam(':nombre', $cliente->getNombres());
        $consulta->bindParam(':apellidos', $cliente->getApellidos());
        $consulta->bindParam(':direccion', $cliente->getDireccion());
        $consulta->bindParam(':latitud', $cliente->getUbi_Latitud());
        $consulta->bindParam(':longitud', $cliente->getUbi_Longitud());
        $consulta->execute();
        header("Location:../vistas/indexCliente.php");
  
    }


    public function EliminarCliente($Cedula){

        $consulta = $this->dbConexion->prepare('DELETE FROM Clientes WHERE Cedula = :cedula');
        $consulta->bindParam(':cedula', $Cedula);
        $consulta->execute();
        $registro = $consulta->fetch();
        return $registro;
        
    }


}
?>
