<?php
$host = 'localhost';  
$user = 'root';       
$password = ''; 
$dbname = 'uts_gis'; 

// Create connection
$connection = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
