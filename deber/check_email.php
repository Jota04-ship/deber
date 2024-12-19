<?php
include 'includes/db.php'; // Ajusta la ruta según tu estructura

$correo = 'jtoalombo7400@uta.edu.ec'; // Cambia esto al correo que quieres verificar

$query = "SELECT * FROM usuarios WHERE correo='$correo'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "El correo existe en la base de datos.";
} else {
    echo "Correo no encontrado.";
}

mysqli_close($conn);
?>
