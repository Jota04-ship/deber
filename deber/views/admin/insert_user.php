<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../includes/db.php';

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
    $tipo = $_POST['tipo'];

    // Verifica si el correo ya existe
    $query = "SELECT * FROM usuarios WHERE correo='$correo'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "El correo ya está en uso.";
    } else {
        $query = "INSERT INTO usuarios (nombre, correo, contraseña, tipo) VALUES ('$nombre', '$correo', '$contraseña', '$tipo')";
        
        if (mysqli_query($conn, $query)) {
            echo "Usuario creado exitosamente.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
</head>
<body>
    <h1>Crear Nuevo Usuario</h1>
    <form method="POST" action="">
        Nombre: <input type="text" name="nombre" required><br>
        Correo: <input type="email" name="correo" required><br>
        Contraseña: <input type="password" name="contraseña" required><br>
        Tipo: <select name="tipo" required>
            <option value="estudiante">Estudiante</option>
            <option value="profesor">Profesor</option>
            <option value="administrador">Administrador</option>
        </select><br>
        <button type="submit">Crear Usuario</button>
    </form>
    <a href="admin_dashboard.php">Volver al panel del administrador</a>
</body>
</html>
