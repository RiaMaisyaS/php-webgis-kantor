<?php
// Menghapus data berdasarkan ID
$id = $_GET['id'];

$query = "DELETE FROM kantor_desa WHERE id = $id";

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "uts_gis");

if (mysqli_query($conn, $query)) {
    header("Location: dashboard.php?delete=success");
    exit(); 
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
