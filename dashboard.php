<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "uts_gis");

function send_query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query Failed:" . mysqli_error($conn));
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

$query = "SELECT 
    id, 
    nama_kantor_desa, 
    kepala_desa, 
    jumlah_penduduk,
    akses_jalan,
    ST_X(coordinate) as lat, 
    ST_Y(coordinate) as lng 
    FROM kantor_desa";

// Count the number of office data
$count_query = "SELECT COUNT(*) as total FROM kantor_desa";
$count_result = send_query($count_query);
$total_kantor_desa = $count_result[0]['total'];

$kantor_desa = send_query($query);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        body {
            font-family:Arial, Helvetica, sans-serif;
            background-image: url('map.bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            color: white;
        }
        
        .navbar {
            background: #c19a6b;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            background: #c19a6b;
            font-weight: bold;
            font-family:Verdana, Geneva, Tahoma, sans-serif;
            font-size: 1.5rem;
            color: #333;
        }

        .navbar-light .navbar-nav .nav-link {
            color: #333;
            padding: 10px 15px;
            background: #c19a6b;
            font-weight: bold; /* Make nav links bold */
        }

        .navbar-light .navbar-nav .nav-link:hover {
            background: #c19a6b;
            color: #ff4d4d; 
        }

        .logout-btn {
            background-color: #ff4d4d;
            color: white;
            padding: 5px 15px; 
            font-size: 0.7rem; 
            border-radius: 15px; 
            transition: background-color 0.3s, transform 0.2s;
            margin-left: auto; 
        }

        .logout-btn:hover {
            background-color: #ff1a1a;
            transform: scale(1.05);
        }

        #map {
            height: 500px; /* Adjusted height of the map */
            border-radius: 20px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            margin-top: 20px;
        }

        .card {
            border: none; /* Optional: removes card border */
        }

        .table {
            background-color: #fff; /* Set the table background color */
        }

        th, td {
            padding: 12px; /* Padding for better spacing */
        }

        th {
            background-color: #c19a6b; /* Header background color */
            color: white; /* White text for header */
        }

        tbody tr {
            background-color: #f8f9fa; /* Light grey for rows */
        }

        tbody tr:nth-child(even) {
            background-color: #e9ecef; /* Slightly darker grey for even rows */
        }

        tbody tr:hover {
            background-color: #dcdcdc; /* Darker grey on hover */
        }

        .aksi-icons a {
            margin-right: 10px; 
            color: white; /* Change icon color to white */
        }

        .aksi-icons {
            min-width: 80px; 
        }

        h5 {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 10px; 
            color:black;
        }

        footer {
            background-color: #c19a6b;
            padding: 10px;
            text-align: center;
            
            width: 100%;
            color: #333;
        }
        .nav-link {
            color: white; /* White text color */
            font-family: 'Arial', sans-serif; /* Font family */
            font-size: 1.1rem; /* Increase font size */
        }
        .nav-link i {
            margin-right: 5px; 
            color: white;
        }

    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark">
  <div class="container-fluid">
  <a class="navbar-brand">
            <img src="logo.png" alt="Logo" height="40"> GIS Kantor Desa Kecamatan Hulu Gurung
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation" style="background-color: #343a40; border: none; color: white;">
    <span class="navbar-toggler-icon" style="background-color: #343a40;"></span>
</button>

    <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel" style="color: #333;">
      <div class="offcanvas-header">
      <span class="nav-link"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['username']); ?></span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
    <ul class="navbar-nav justify-content-end flex-grow-1 pe-2">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="create.html">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

    </div>
  </div>
</nav>

<div class="container mt-1 pt-3"> 

    <!-- Map section without any container or box styling -->
    <div id="map" style="height: 500px; border-radius: 10px;"></div>
</div>




<footer class="mt-3 pt-3" style="background-color: #343a40; color: white; text-align: center; padding: 20px; border-top: 2px solid #c19a6b;">
    <p class="mb-0">&copy; <?= date('Y'); ?> GIS Kantor Desa Kecamatan Hulu Gurung. All Rights Reserved.</p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize the map
    var map = L.map('map').setView([0.3920553808732141, 112.34767379898561], 12); // Set map position as needed

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var kantor_desa = <?= json_encode($kantor_desa) ?>;
    kantor_desa.forEach(function(kantor) {
        L.marker([kantor.lat, kantor.lng]).addTo(map)
            .bindPopup(
                "<b>" + kantor.nama_kantor_desa + "</b><br>" +
                "<strong>Kepala Desa:</strong> " + kantor.kepala_desa + "<br>" +
                "<strong>Jumlah Penduduk:</strong> " + kantor.jumlah_penduduk + "<br>" +
                "<strong>Akses Jalan:</strong> " + kantor.akses_jalan + "<br>" +
                "<a href='update.php?id=" + kantor.id + "' class='btn btn-success'>" +
                "<i class='fas fa-edit text-white fa-sm'></i></a> " +
                "<a href='delete.php?id=" + kantor.id + "' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>" +
                "<i class='fas fa-trash-alt text-white fa-sm'></i></a>"
            );
    });
</script>
</body>
</html>
