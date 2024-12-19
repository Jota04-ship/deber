<?php
include 'includes/db.php'; // Asegúrate de que la ruta sea correcta

// Cambia esto por la dirección de correo del usuario que deseas verificar
$correo = 'jtoalombo7400@uta.edu.ec'; 
$password = 'juanK2004'; // La contraseña que quieres verificar

// Consulta para obtener el hash de la contraseña
$query = "SELECT contraseña FROM usuarios WHERE correo='$correo'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $hashedPassword = $row['contraseña']; // Obtiene el hash de la base de datos

    // Verifica la contraseña
    if (password_verify($password, $hashedPassword)) {
        echo "Contraseña correcta.";
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Correo no encontrado.";
}

mysqli_close($conn);
?>
