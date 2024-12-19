<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Eliminar clase si se solicita
if (isset($_GET['delete_id'])) {
    $clase_id = $_GET['delete_id'];
    $query = "DELETE FROM clases WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clase_id);
    if ($stmt->execute()) {
        echo "Clase eliminada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener todas las clases con el nombre de la materia y del profesor
$query = "SELECT c.id AS clase_id, m.nombre AS materia_nombre, u.nombre AS profesor_nombre, c.nombre_clase 
          FROM clases c 
          JOIN materias m ON c.materia_id = m.id 
          JOIN usuarios u ON c.profesor_id = u.id";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Clases</title>
</head>
<body>
    <h1>Lista de Clases</h1>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li>
                Clase: <?php echo $row['nombre_clase'] . " " . $row['materia_nombre']; ?> - Profesor: <?php echo $row['profesor_nombre']; ?>
                <a href="edit_clases.php?id=<?php echo $row['clase_id']; ?>">Editar</a>
                <a href="?delete_id=<?php echo $row['clase_id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta clase?');">Eliminar</a>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="admin_dashboard.php">Volver al panel del administrador</a>
</body>
</html>
