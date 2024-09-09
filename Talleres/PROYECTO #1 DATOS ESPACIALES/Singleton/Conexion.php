<!-- Aqui usaremos singleton para crear una unica instancia de conexion -->
<?php
class Conexion extends PDO {
    private static $instancia = null;
    private $tipo_da_base = "mysql";
    private $host = "localhost";
    private $nombre_de_base = "DatosEspaciales";
    private $usuario = "root";
    private $contrasena = "";

    public function __construct()
    {
        try {
            parent::__construct("{$this->tipo_da_base}:dbname={$this->nombre_de_base};
            host={$this->host};charset=utf8", $this->usuario, $this->contrasena);
        } catch (PDOException $e) {
            echo "Ha surgio un error y no se puede conectar a la base de datos. Detalle: ". $e->getMessage();
            exit;
        }
    }

    //Aqui obtenemos la unica instancia 
    //usa self porue llama a la misma clase 
    //el if es pera definir si ya se creo una instancia, si no es asi la crea, si ya hay una la retorna
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

}

// Llamar al método estático para obtener la única instancia de Conexion
$db = Conexion::getInstancia();

?>