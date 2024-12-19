<?php
session_start();
include 'includes/db.php';

// Verifica que el usuario esté logueado y sea administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

$query = "DELETE FROM usuarios WHERE id='$id'";
if (mysqli_query($conn, $query)) {
    echo "Usuario eliminado exitosamente.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
header("Location: manage_users.php");
exit();
?>
