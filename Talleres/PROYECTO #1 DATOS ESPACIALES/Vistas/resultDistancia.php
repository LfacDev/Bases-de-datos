<?php
    require('../Singleton/Conexion.php');
    require_once('../DAO/ClienteDAO.php');
    // Obtén la conexión de base de datos usando el Singleton
    $db = Conexion::getInstancia();
    // Crea una instancia del DAO con la conexión de base de datos
    $ClienteDAO = new ClienteDAO($db);

    $lat = $_GET['lat'];
    $lng = $_GET['lng'];

    // Llama al método mostrarDistancia con las coordenadas
    $ClienteDTO = $ClienteDAO->mostrarDistancia($lat, $lng);

    
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
    <div class="container">
        <div class="container-fluid">
            <!-- Page Heading -->
            <br>
            <h1>Lista de Clientes</h1>
            <br>
            <a href="../vistas/distanciaCliente.php" class=".bg-gradient-success btn btn-success btn-lg  text-decoration-none">Volver</a>
            <hr>
            <table id="tabla" class="table table-striped table-bordered" style="width:100%" >
                    <thead>
                        <tr>
                            <th>Ranking</th>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Distancia</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ClienteDTO as $Cliente){ ?>
                            <tr>
                                <td> <?php echo $Cliente['Ranking']; ?></td>
                                <td> <?php echo $Cliente['Cedula'];?></td>
                                <td> <?php echo $Cliente['Nombre'];?></td>                               
                                <td> <?php echo $Cliente['Distancia']; ?></td>
                            </tr>
                    <?php } ?>   
                    </tbody>
            </table>
        </div>
    </div>
</body>