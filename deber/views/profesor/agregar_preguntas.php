<?php
session_start();

// Asegúrate de que la ruta de inclusión es correcta
include '../../includes/db.php';

// Verifica si el usuario es un profesor
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del examen desde la URL
if (!isset($_GET['examen_id'])) {
    die("No se ha proporcionado examen_id.");
}

$examen_id = $_GET['examen_id'];

// Manejo del formulario de agregar preguntas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pregunta'])) {
    $pregunta = $_POST['pregunta'];

    // Validar que la pregunta no esté vacía
    if (empty(trim($pregunta))) {
        echo "La pregunta no puede estar vacía.";
    } else {
        // Insertar pregunta en la base de datos
        $query = "INSERT INTO preguntas (examen_id, pregunta, tipo) VALUES (?, ?, 'respuesta_abierta')";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $conn->error);
        }
        $stmt->bind_param("is", $examen_id, $pregunta);
        
        if ($stmt->execute()) {
            echo "Pregunta agregada exitosamente.<br>";
        } else {
            echo "Error al agregar la pregunta: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Contar preguntas existentes
$queryCount = "SELECT COUNT(*) AS total FROM preguntas WHERE examen_id = ?";
$stmtCount = $conn->prepare($queryCount);
$stmtCount->bind_param("i", $examen_id);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalPreguntas = $resultCount->fetch_assoc()['total'];
$stmtCount->close();

// Obtener todas las preguntas existentes para el examen
$queryPreguntas = "SELECT pregunta FROM preguntas WHERE examen_id = ?";
$stmtPreguntas = $conn->prepare($queryPreguntas);
$stmtPreguntas->bind_param("i", $examen_id);
$stmtPreguntas->execute();
$resultPreguntas = $stmtPreguntas->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Preguntas al Examen</title>
</head>
<body>
    <h1>Agregar Preguntas al Examen</h1>
    <h2>Pregunta <?php echo $totalPreguntas + 1; ?></h2>
    <form method="POST" action="">
        <label for="pregunta">Pregunta:</label>
        <textarea name="pregunta" id="pregunta" required></textarea><br>

        <button type="submit">Agregar Pregunta</button>
    </form>

    <h2>Preguntas Agregadas:</h2>
    <?php if ($totalPreguntas > 0): ?>
        <ul>
            <?php while ($row = $resultPreguntas->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($row['pregunta']); ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No se han agregado preguntas aún.</p>
    <?php endif; ?>

    <form method="POST" action="profesor_dashboard.php">
        <button type="submit">Finalizar y Volver al Panel</button>
    </form>
</body>
</html>
