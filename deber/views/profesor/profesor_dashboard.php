<?php 
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener clases del profesor
$profesor_id = $_SESSION['usuario_id'];
$query = "SELECT c.id AS clase_id, m.nombre AS materia_nombre FROM clases c 
          JOIN materias m ON c.materia_id = m.id 
          WHERE c.profesor_id = '$profesor_id'";
$result = mysqli_query($conn, $query);

// Verificar si se encontraron clases
if (mysqli_num_rows($result) === 0) {
    echo "No tienes clases asignadas.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Profesor</title>
</head>
<body>
    <h1>Panel del Profesor</h1>
    <h2>Mis Clases</h2>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li>
                Clase: <?php echo $row['materia_nombre']; ?>
                <a href="/views/admin/ver_estudiantes.php?clase_id=<?php echo $row['clase_id']; ?>">Ver Estudiantes</a>
                <a href="crear_deber.php?clase_id=<?php echo $row['clase_id']; ?>">Crear Deber</a>
                <a href="crear_examen.php?clase_id=<?php echo $row['clase_id']; ?>">Crear Examen</a>
                <a href="ver_deberes.php?clase_id=<?php echo $row['clase_id']; ?>">Ver Deberes</a>
                <a href="ver_examenes.php?clase_id=<?php echo $row['clase_id']; ?>">Ver Examenes</a>
                <a href="ver_calificaciones.php?clase_id=<?php echo $row['clase_id']; ?>">Ver Calificaciones</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <a href="../../logout.php">Cerrar sesión</a>
</body>
</html>
