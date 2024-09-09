<?php
    require('../Singleton/Conexion.php');
    require_once('../DAO/ClienteDAO.php');
    // Obtén la conexión de base de datos usando el Singleton
    $db = Conexion::getInstancia();
    // Crea una instancia del DAO con la conexión de base de datos
    $ClienteDAO = new ClienteDAO($db);
    // Obtén un usuario
    $ClienteDTO = $ClienteDAO->mostrarClientes();
    
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
            <a href="../vistas/crearCliente.php" class=".bg-gradient-success btn btn-info btn-lg  text-decoration-none">Crear Cliente</a>
            <a href="../vistas/distanciaCliente.php" class=".bg-gradient-success btn btn-info btn-lg  text-decoration-none">Calcular Distancia</a>
            <hr>
            <table id="tabla" class="table table-striped table-bordered" style="width:100%" >
                    <thead>
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Direccion</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ClienteDTO as $Cliente){ ?>
                            <tr>
                                <td> <?php echo $Cliente['Cedula'];?></td>
                                <td> <?php echo $Cliente['Nombre'];?></td>
                                <td> <?php echo $Cliente['Apellidos'];?></td>
                                <td> <?php echo $Cliente['Direccion'];?></td>
                                <td> <?php echo $Cliente['Ubi_Latitud'];?></td>
                                <td> <?php echo $Cliente['Ubi_Longitud'];?></td>
                                <td>
                                    <a class="btn btn-primary" name="editar" href="<?php echo "EditarCliente.php?Cedula=" . $Cliente['Cedula']?>">Editar</a>
                                    <a class="btn btn-danger" name="eliminar" href="<?php echo "EliminarCliente.php?Cedula=" . $Cliente['Cedula'];?>">Eliminar</a>
                                </td>
                                
                            </tr>
                    <?php } ?>   
                    </tbody>
            </table>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready( function () {
        $('#tabla').DataTable();
    } );
</script>

</html>