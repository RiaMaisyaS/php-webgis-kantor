<?php
// Memghubungkan ke database
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "uts_gis"
);

// Memeriksa apakah data POST ada
$nama_kantor_desa = isset($_POST['nama_kantor_desa']) ? $_POST['nama_kantor_desa'] : '';
$kepala_desa = isset($_POST['kepala_desa']) ? $_POST['kepala_desa'] : '';
$jumlah_penduduk = isset($_POST['jumlah_penduduk']) ? $_POST['jumlah_penduduk'] : '';
$akses_jalan = isset($_POST['akses_jalan']) ? $_POST['akses_jalan'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

// Memeriksa apakah semua nilai yang diperlukan sudah ada
    $query = "
    INSERT INTO kantor_desa (nama_kantor_desa, kepala_desa, jumlah_penduduk, akses_jalan, coordinate)
    VALUES (
                '$nama_kantor_desa',
                '$kepala_desa',
                '$jumlah_penduduk',
                '$akses_jalan',
                ST_GeomFromText('POINT($latitude $longitude)', 4326)
            )
        ";

        

    // Menjalankan query dan menangani kesalahan
    if (mysqli_query($conn, $query)) {
        header(header:"Location: dashboard.php");
    } else {
        $message = "Kesalahan saat menyimpan data: " . mysqli_error($conn);
    }

