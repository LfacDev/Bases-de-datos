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
    <style>
        #miMapa{
            height: 600px;
            width: 600px;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link href="../issets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
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
<br>
<a class="btn btn-primary" href="indexCliente.php">Volver</a>
    <br>
    <br>
    <br>
    <h1 class="h3 mb-4 text-gray-800">Editar Clientes</h1>
    <div class="container-fluid">
        <div class="row">
            <form method="POST" action="../Controladores/ControladorCliente.php" class="col-6">
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
                <input  required type="text" value="<?php echo $ClienteDTO['Ubi_Latitud'] ?>" class="form-control" placeholder="Telefono" aria-label="Username" aria-describedby="basic-addon1" type="text" name="latitud" id="latitud"/>
                <h1 class="h3 mb-4 text-gray-800">Longitud</h1>
                <input  required type="text" value="<?php echo $ClienteDTO['Ubi_Longitud'] ?>" class="form-control" placeholder="Telefono" aria-label="Username" aria-describedby="basic-addon1" type="text" name="longitud" id="longitud"/>
                <p></p>
                <button name="editar" type="submit" class="btn btn-success btn-lg">Enviar</button>
            </form>
            <div class="col-6">
                <h2>Mapa</h2>
                <div id="miMapa"></div>
                </div>
        </div>
    </div>
    <br>    
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let map = L.map('miMapa').setView([4.631826, -74.080483],19)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);


        map.on('click', onMapClick);
        function onMapClick(e){
            let latitud = e.latlng.lat;  
            let longitud = e.latlng.lng;
            document.getElementById("latitud").value = latitud;
            document.getElementById("longitud").value = longitud;

            L.marker([latitud, longitud]).addTo(map)
            .bindPopup('A pretty CSS popup.<br> Easily customizable.')
            .openPopup();
        }
        
    </script>
</body>
</html>