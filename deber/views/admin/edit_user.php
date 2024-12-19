<?php
session_start();
include 'includes/db.php';

// Verifica que el usuario esté logueado y sea administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM usuarios WHERE id='$id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $tipo = $_POST['tipo'];

    $update_query = "UPDATE usuarios SET nombre='$nombre', correo='$correo', tipo='$tipo' WHERE id='$id'";
    if (mysqli_query($conn, $update_query)) {
        echo "Usuario actualizado exitosamente.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <h2>Editar Usuario</h2>
    <form method="POST" action="">
        Nombre: <input type="text" name="nombre" value="<?php echo $user['nombre']; ?>" required>
        Correo: <input type="email" name="correo" value="<?php echo $user['correo']; ?>" required>
        Tipo: <select name="tipo" required>
            <option value="estudiante" <?php echo $user['tipo'] === 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
            <option value="profesor" <?php echo $user['tipo'] === 'profesor' ? 'selected' : ''; ?>>Profesor</option>
        </select>
        <button type="submit">Actualizar Usuario</button>
    </form>
    <a href="manage_users.php">Volver a la Gestión de Usuarios</a>
</body>
</html>

<?php
mysqli_close($conn);
?>
