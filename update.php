<?php
// Menerima id dari read.php
$id = $_GET['id'];

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "uts_gis");

function send_query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
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
    FROM kantor_desa WHERE id = $id";
$village = send_query($query);

if (empty($village)) {
    die("village not found.");
}
$village = $village[0];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kantor_desa = mysqli_real_escape_string($conn, $_POST['nama_kantor_desa']);
    $kepala_desa = mysqli_real_escape_string($conn, $_POST['kepala_desa']);
    $jumlah_penduduk = mysqli_real_escape_string($conn, $_POST['jumlah_penduduk']);
    $akses_jalan = mysqli_real_escape_string($conn, $_POST['akses_jalan']);
    
    $update_query = "UPDATE kantor_desa SET nama_kantor_desa='$nama_kantor_desa', kepala_desa='$kepala_desa',
    jumlah_penduduk='$jumlah_penduduk',akses_jalan='$akses_jalan' WHERE id=$id";
    if (mysqli_query($conn, $update_query)) {
        echo "Record updated successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Kantor Desa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


    <style>
        body {
            background-image: url('map.bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
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
        .container {
            max-width: 900px;
        }
        #map {
            height: 450px; /* Menyesuaikan tinggi peta */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card {
            display: flex;
            flex-direction: column; /* Align items vertically */
            height: 100%; /* Allow cards to stretch to fill the row */
            background-color: #c19a6b; /* Background color for card */
            border-radius: 10px; /* Rounded corners */
            padding: 15px; /* Padding inside the card */
        }
        h5 {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-style: inherit;
        }
        footer {
            color: antiquewhite;
        }
        .form-control {
            background-color: #f7f7f7; /* Light background for form inputs */
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
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
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
    
<div class="container mt-5">
        <div class="row">
        <!-- Map Card -->
       

        <div class="col-sm-6 mb-3 mb-sm-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Kantor Desa</h5>
                    <hr>
                    <form action="process-update.php" method="post">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($village['id']); ?>">

                        <div class="form-group">
                            <label for="nama_kantor_desa">Nama Kantor Desa</label>
                            <input type="text" id="nama_kantor_desa" name="nama_kantor_desa" class="form-control" value="<?= htmlspecialchars($village['nama_kantor_desa']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kepala_desa">Kepala Desa</label>
                            <input type="text" id="kepala_desa" name="kepala_desa" class="form-control" value="<?= htmlspecialchars($village['kepala_desa']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_penduduk">Jumlah Penduduk</label>
                            <input type="number" id="jumlah_penduduk" name="jumlah_penduduk" class="form-control" value="<?= htmlspecialchars($village['jumlah_penduduk']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="akses_jalan">Akses Jalan</label>
                            <select id="akses_jalan" name="akses_jalan" class="form-control" required>
                                <option value="aspal" <?= $village['akses_jalan'] === 'aspal' ? 'selected' : ''; ?>>Aspal</option>
                                <option value="tanah" <?= $village['akses_jalan'] === 'tanah' ? 'selected' : ''; ?>>Tanah</option>
                                <option value="bebatuan" <?= $village['akses_jalan'] === 'bebatuan' ? 'selected' : ''; ?>>Bebatuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control" value="<?= htmlspecialchars($village['lat']); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" value="<?= htmlspecialchars($village['lng']); ?>" readonly>
                        </div>

                        <button type="submit" class="btn btn-warning mt-2">Update</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Peta Lokasi</h5>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
    </div>
</div>

<footer class="mt-3 pt-3" style="background-color: #343a40; color: white; text-align: center; padding: 20px; border-top: 2px solid #c19a6b;">
    <p class="mb-0">&copy; <?= date('Y'); ?> GIS Kantor Desa Kecamatan Hulu Gurung. All Rights Reserved.</p>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([<?= htmlspecialchars($village['lat']); ?>, <?= htmlspecialchars($village['lng']); ?>], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker;
        var inputLatitude = document.getElementById('latitude');
        var inputLongitude = document.getElementById('longitude');

        if (<?= json_encode($village) ?>) {
            marker = L.marker([<?= htmlspecialchars($village['lat']); ?>, <?= htmlspecialchars($village['lng']); ?>]).addTo(map);
        }

        map.on('click', function (e) {
            if (marker) { map.removeLayer(marker); }
            marker = L.marker(e.latlng).addTo(map);
            inputLatitude.value = e.latlng.lat;
            inputLongitude.value = e.latlng.lng;
        });
    </script>
</body>
</html>
