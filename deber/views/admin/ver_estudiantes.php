<?php
session_start();
include '../../includes/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tipo'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener todas las clases según el tipo de usuario
$tipo_usuario = $_SESSION['tipo'];
if ($tipo_usuario === 'administrador') {
    $queryClases = "SELECT * FROM clases";
} else if ($tipo_usuario === 'profesor') {
    $profesor_id = $_SESSION['usuario_id'];
    $queryClases = "SELECT c.* FROM clases c WHERE c.profesor_id = ?";
}

// Preparar la consulta de clases
$stmtClases = $conn->prepare($queryClases);
if ($tipo_usuario === 'profesor') {
    $stmtClases->bind_param("i", $profesor_id);
}
$stmtClases->execute();
$resultClases = $stmtClases->get_result();

// Procesar el formulario si se selecciona una clase
$estudiantes = [];
if (isset($_POST['clase_id'])) {
    $clase_id = $_POST['clase_id'];
    
    // Obtener estudiantes matriculados en la clase seleccionada
    $queryEstudiantes = "SELECT e.id, e.nombre FROM usuarios e
                         JOIN matriculas m ON e.id = m.estudiante_id
                         WHERE m.clase_id = ?";
    $stmt = $conn->prepare($queryEstudiantes);
    if ($stmt === false) {
        die('Error en la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $clase_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Estudiantes</title>
</head>
<body>
    <h1>Ver Estudiantes</h1>

    <form method="POST" action="">
        <label for="clase_id">Selecciona una clase:</label>
        <select name="clase_id" id="clase_id" required>
            <option value="">Seleccione una clase</option>
            <?php while ($row = $resultClases->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_clase']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Ver Estudiantes</button>
    </form>

    <?php if (!empty($estudiantes)): ?>
        <h2>Estudiantes en la clase seleccionada:</h2>
        <ul>
            <?php foreach ($estudiantes as $estudiante): ?>
                <li><?php echo $estudiante['nombre']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (isset($_POST['clase_id'])): ?>
        <p>No hay estudiantes matriculados en esta clase.</p>
    <?php endif; ?>

    <a href="<?php echo ($tipo_usuario === 'administrador') ? '../admin/admin_dashboard.php' : '../profesor/profesor_dashboard.php'; ?>">Volver al panel</a>
</body>
</html>
