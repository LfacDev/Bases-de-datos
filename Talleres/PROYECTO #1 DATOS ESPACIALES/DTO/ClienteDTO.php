<!-- Aqui crearemos el objeto DTO -->
<?php

Class ClienteDTO{
    private $Cedula;
    private $Nombres;
    private $Apellidos;
    private $Direccion;
    private $Ubi_Latitud;
    private $Ubi_Longitud;
    
    public function __construct($Cedula=null, $Nombres=null, $Apellidos=null, $Direccion=null, $Ubi_Latitud=null, $Ubi_Longitud=null){
        $this->Cedula = $Cedula;
        $this->Nombres = $Nombres;
        $this->Apellidos = $Apellidos;
        $this->Direccion = $Direccion;
        $this->Ubi_Latitud = $Ubi_Latitud;
        $this->Ubi_Longitud = $Ubi_Longitud;
    }

    public function getCedula() {
        return $this->Cedula;
    }

    public function getNombres() {
        return $this->Nombres;
    }

    public function getApellidos() {
        return $this->Apellidos;
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function getUbi_Latitud() {
        return $this->Ubi_Latitud;
    }

    public function getUbi_Longitud() {
        return $this->Ubi_Longitud;
    }
}