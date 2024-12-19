<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// Inserción de nueva materia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];

    // Verifica si la materia ya existe
    $query = "SELECT * FROM materias WHERE nombre='$nombre'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        echo "La materia ya existe.";
    } else {
        $query = "INSERT INTO materias (nombre) VALUES ('$nombre')";
        if (mysqli_query($conn, $query)) {
            echo "Materia creada exitosamente.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Obtener todas las materias
$query = "SELECT * FROM materias";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Materias</title>
</head>
<body>
    <h1>Administrar Materias</h1>
    
    <h2>Crear Nueva Materia</h2>
    <form method="POST" action="">
        Nombre de la Materia: <input type="text" name="nombre" required>
        <button type="submit">Crear Materia</button>
    </form>
    
    <h2>Lista de Materias</h2>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li><?php echo htmlspecialchars($row['nombre']); ?></li>
        <?php endwhile; ?>
    </ul>

    <a href="admin_dashboard.php">Volver al panel del administrador</a>
</body>
</html>
