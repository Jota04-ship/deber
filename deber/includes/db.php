<?php
$servername = "127.0.0.1";
$username = "root";
$password = "juanK2004"; // Cambia según tu configuración
$dbname = "universidad";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
