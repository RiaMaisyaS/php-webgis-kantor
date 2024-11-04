<?php
// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "uts_gis");

// Memeriksa koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Menangkap semua data dari form
$id = $_POST['id'];
$nama_kantor_desa = $_POST['nama_kantor_desa'];
$kepala_desa = $_POST['kepala_desa'];
$jumlah_penduduk = $_POST['jumlah_penduduk'];
$akses_jalan = $_POST['akses_jalan'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$query = "UPDATE kantor_desa 
SET 
    nama_kantor_desa = '$nama_kantor_desa',
    kepala_desa = '$kepala_desa',
    jumlah_penduduk = '$jumlah_penduduk',
    akses_jalan = '$akses_jalan',
    coordinate = ST_GeomFromText('POINT($latitude $longitude)', 4326)
WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: dashboard.php?update=success");
    exit(); 
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
