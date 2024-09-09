<?php
//Este controlador lo creamos para recibir las peticiones, verificar que los datos esten llegando y comunicarse con el DAO para realizar las operaciones que este tiene, podemos decir que es un intermediario entre el DAO y las vistas  
    require_once('../Singleton/Conexion.php');
    require_once('../DAO/ClienteDAO.php');
    require_once('../DTO/ClienteDTO.php');
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["crear"])) {
            // Obtén la conexión de base de datos usando el Singleton
            $db = Conexion::getInstancia();
                    
            // Crea una instancia del DAO con la conexión de base de datos
            $clienteDAO = new ClienteDAO($db);

            //guarda los datos del formulario en variables
            if(isset($_POST["cedula"]) && isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["direccion"]) && isset($_POST["ubi_latitud"]) && isset($_POST["ubi_longitud"])){
        
                $cedula = htmlentities(addslashes($_POST["cedula"]));
                $nombre = htmlentities(addslashes($_POST["nombre"]));
                $apellidos = htmlentities(addslashes($_POST["apellidos"]));
                $direccion = htmlentities(addslashes($_POST["direccion"]));
                $ubi_latitud = htmlentities(addslashes($_POST["ubi_latitud"]));
                $ubi_longitud = htmlentities(addslashes($_POST["ubi_longitud"]));

                var_dump($_POST);
        
                //crea el obtejo de tipo DTO y llama la funcion crear
                if(!empty($cedula) && !empty($nombre)){
        
                    $clienteDTO = new ClienteDTO($cedula, $nombre, $apellidos, $direccion, $ubi_latitud, $ubi_longitud );
                    $clienteDAO->CrearCliente($clienteDTO);
        
                }else{
                    $mensaje_error = 'Todos los campos son requeridos';
                }
        
            }else{
                $mensaje_error = 'Error en los datos proporcionados';
            }
        }else if (isset($_POST["editar"])){

            //guarda los datos del formulario en variables
            if(isset($_POST["cedula"]) && isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["direccion"]) && isset($_POST["latitud"]) && isset($_POST["longitud"])){

                $clienteDAO = new ClienteDAO($db);
        
                $cedula = htmlentities(addslashes($_POST["cedula"]));
                $nombre = htmlentities(addslashes($_POST["nombre"]));
                $apellidos = htmlentities(addslashes($_POST["apellidos"]));
                $direccion = htmlentities(addslashes($_POST["direccion"]));
                $ubi_latitud = htmlentities(addslashes($_POST["latitud"]));
                $ubi_longitud = htmlentities(addslashes($_POST["longitud"]));

                var_dump($_POST);
        
                //crea el obtejo de tipo DTO y llama la funcion editar
                if(!empty($cedula) && !empty($nombre)){
        
                    $clienteDTO = new ClienteDTO($cedula, $nombre, $apellidos, $direccion, $ubi_latitud, $ubi_longitud );
                    $clienteDAO->EditarCliente($clienteDTO);
        
                }else{
                    $mensaje_error = 'Todos los campos son requeridos';
                }
        
            }else{
                $mensaje_error = 'Error en los datos proporcionados';
            }

        }elseif (isset($_POST["eliminar"])) {
            $clienteDAO = new ClienteDAO($db);
            $CedulaCliente = $_POST["cedula"]; 
            $clienteDAO->EliminarCliente($CedulaCliente);
             header("Location: ../vistas/indexCliente.php");

        }elseif (isset($_POST["distancia"])) {
            echo "entre";

            $clienteDAO = new ClienteDAO($db);
            // Verifica que los campos Ubi_Latitud y Ubi_Longitud existan
            if (isset($_POST["Ubi_Latitud"]) && isset($_POST["Ubi_Longitud"])) {
                echo "verifique";
                // Guarda los datos del formulario en variables
                $ubi_latitud = htmlentities(addslashes($_POST["Ubi_Latitud"]));
                $ubi_longitud = htmlentities(addslashes($_POST["Ubi_Longitud"]));

                // Depuración: Muestra los valores de latitud y longitud
                var_dump($ubi_latitud, $ubi_longitud);
                echo "guarde";
                // Crea el objeto ClienteDTO
                $clienteDTO = new ClienteDTO($ubi_latitud, $ubi_longitud);

                // Llama al método para mostrar la distancia
                $clienteDAO->mostrarDistancia($ubi_latitud, $ubi_longitud);
                echo "como mande";
                // Redirige a la vista con los resultados
                header("Location: ../vistas/resultDistancia.php?lat=$ubi_latitud&lng=$ubi_longitud");
                exit();
                echo "redirigi";
            }
        }else {
                // Agregar código de depuración
                echo "El formulario no fue enviado desde el botón correcto.";
            }     
    }
?>
