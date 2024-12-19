<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al formulario de inicio de sesión
header("Location: index2.php"); // Asegúrate de que la ruta sea correcta
exit();
?>
