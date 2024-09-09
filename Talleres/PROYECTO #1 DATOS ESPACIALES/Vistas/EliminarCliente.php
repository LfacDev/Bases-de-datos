<?php
    require_once('../Controladores/ControladorCliente.php');
    require_once('../Singleton/Conexion.php');
    require_once('../DAO/ClienteDAO.php');
    // Obtén la conexión de base de datos usando el Singleton
    $db = Conexion::getInstancia();
    // Crea una instancia del DAO con la conexión de base de datos
    $ClienteDAO = new ClienteDAO($db);
    // Obtén un usuario
    $cedula = $_GET['Cedula'];
    $ClienteDTO = $ClienteDAO->mostrarClienteCedula($cedula);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link href="../issets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <title>Document</title>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #337aff;">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.html">Clientes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="../index.html">Inicio</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="../vistas/indexCliente.php">Lista Clientes</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="../vistas/crearCliente.php">Crear Cliente</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="../vistas/distanciaCliente.php">Calcular Distancia</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
<!-- Begin Page Content -->
<div class="container my-4">

<a class="btn btn-primary" href="indexCliente.php">Volver</a>
    <br>
    <br>
    <br>
    <h1 class="h3 mb-4 text-gray-800">Eliminar Cliente</h1>
    <form method="POST" action="../Controladores/ControladorCliente.php">
    <input type="text" value="<?php echo $cedula ?>" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1" type="text" name="cedula" />
        <h1 class="h3 mb-4 text-gray-800">Nombre</h1>
        <input required type="text" value="<?php echo $ClienteDTO['Nombre'] ?>" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1" type="text" name="nombre" />
        <hr>
        <h1 class="h3 mb-4 text-gray-800">Apellidos</h1>
        <input required type="text"  value="<?php echo $ClienteDTO['Apellidos'] ?>" class="form-control" placeholder="Direccion" aria-label="Username" aria-describedby="basic-addon1" type="text" name="apellidos"/>
        <hr>
        <h1 class="h3 mb-4 text-gray-800">Direccion</h1>
        <input  required type="text" value="<?php echo $ClienteDTO['Direccion'] ?>" class="form-control" placeholder="Telefono" aria-label="Username" aria-describedby="basic-addon1" type="text" name="direccion"/>
        <h1 class="h3 mb-4 text-gray-800">Latitud</h1>
        <input  required type="text" value="<?php echo $ClienteDTO['Ubi_Latitud'] ?>" class="form-control" placeholder="Telefono" aria-label="Username" aria-describedby="basic-addon1" type="text" name="latitud"/>
        <h1 class="h3 mb-4 text-gray-800">Longitud</h1>
        <input  required type="text" value="<?php echo $ClienteDTO['Ubi_Longitud'] ?>" class="form-control" placeholder="Telefono" aria-label="Username" aria-describedby="basic-addon1" type="text" name="longitud"/>
        <p></p>
        <button name="eliminar" type="submit" class="btn btn-success btn-lg">Eliminar</button>
    </form>
    <br>
    <?php 

        if (isset($_SESSION['error_mensaje'])) {
            $error_mensaje = $_SESSION['error_mensaje'];
            echo "<div class='alert alert-danger' role='alert''>$error_mensaje</div>";
            unset($_SESSION['error_mensaje']);  // Limpiar la variable de sesión después de mostrar el mensaje
        }
    ?>

    
</div>
</body>
</html>