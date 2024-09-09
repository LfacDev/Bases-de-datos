<?php
    require_once('../Singleton/Conexion.php');
    require_once('../DAO/ClienteDAO.php');
    require_once('../DTO/ClienteDTO.php');
    require_once('../Controladores/ControladorCliente.php');
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link href="../issets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <title>Crear Cliente</title>

    <style>
        #miMapa{
            height: 600px;
            width: 600px;
        }
    </style>
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

    <div class="container">
        <br>
        <a class=".bg-gradient-primary btn btn-primary btn-lg  text-decoration-none" href="indexCliente.php">Volver</a>
        <br>
        <br>
        <h1>Crear Clientes</h1>
        <hr>
        <div class="container-fluid">
            <div class="row">
                <form method="POST" action="../Controladores/ControladorCliente.php" class="col-6">
                    <h2 class="h4 mb-3 text-gray-800">Ubicacion - Latitud</h2>
                    <input type="text" class="form-control col-10" placeholder="Latitud" aria-label="Latitud" name="Ubi_Latitud" id="ubi_latitud"/>
                    <br>
                    <h2 class="h4 mb-3 text-gray-800">Ubicacion - Longitud</h2>
                    <input type="text" class="form-control col-10" placeholder="Longitud" aria-label="Longitud" name="Ubi_Longitud" id="ubi_longitud"/>
                    <br>
                    <p></p>
                    <button type="submit" class="btn btn-success col-4 btn-lg" name="distancia">Enviar</button>
                </form>
                <div class="col-6">
                    <h2>Mapa</h2>
                    <div id="miMapa"></div>
                </div>
            </div>
            <br>
        </div>
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
            document.getElementById("ubi_latitud").value = latitud;
            document.getElementById("ubi_longitud").value = longitud;

            L.marker([latitud, longitud]).addTo(map)
            .bindPopup('A pretty CSS popup.<br> Easily customizable.')
            .openPopup();
        }
        
    </script>
</body>
    
    </html>

