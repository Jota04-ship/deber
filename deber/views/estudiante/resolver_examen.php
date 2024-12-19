<?php
session_start();

// Verifica que la sesión esté activa y que el usuario sea estudiante
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'estudiante') {
    header("Location: ../index.php");
    exit();
}

// Incluye la conexión a la base de datos (verifica que la ruta sea correcta)
include '../../includes/db.php';  // Asegúrate de que la ruta sea correcta

// Verifica si se ha proporcionado un `examen_id` en la URL
if (!isset($_GET['examen_id'])) {
    die("No se ha proporcionado examen_id.");
}

$examen_id = $_GET['examen_id'];
$usuario_id = $_SESSION['usuario_id']; // ID del usuario que está logueado

// Prepara la consulta para obtener las preguntas del examen
$queryPreguntas = "
    SELECT p.id AS pregunta_id, p.pregunta, r.respuesta
    FROM preguntas p
    LEFT JOIN respuestas r ON p.id = r.pregunta_id AND r.estudiante_id = ?
    WHERE p.examen_id = ?";

$stmtPreguntas = $conn->prepare($queryPreguntas);

if ($stmtPreguntas === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincula los parámetros y ejecuta la consulta
$stmtPreguntas->bind_param("ii", $usuario_id, $examen_id);
$stmtPreguntas->execute();
$resultPreguntas = $stmtPreguntas->get_result();

// Si el formulario ha sido enviado, guarda las respuestas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si hay respuestas para guardar
    if (isset($_POST['respuestas']) && is_array($_POST['respuestas'])) {
        foreach ($_POST['respuestas'] as $pregunta_id => $respuesta) {
            $query = "INSERT INTO respuestas (examen_id, pregunta_id, estudiante_id, respuesta) 
                      VALUES (?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta)";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error en la preparación de la consulta de respuestas: " . $conn->error);
            }
            $stmt->bind_param("iiis", $examen_id, $pregunta_id, $usuario_id, $respuesta);
            $stmt->execute();
        }
        echo "Examen enviado exitosamente.";
    } else {
        echo "No has respondido ninguna pregunta.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolver Examen</title>
</head>
<body>
    <h1>Resolver Examen</h1>

    <?php if ($resultPreguntas->num_rows > 0): ?>
        <form method="POST">
            <?php while ($row = $resultPreguntas->fetch_assoc()): ?>
                <div>
                    <label><?php echo htmlspecialchars($row['pregunta']); ?></label>
                    <textarea name="respuestas[<?php echo $row['pregunta_id']; ?>]" required>
                        <?php echo htmlspecialchars($row['respuesta']); ?>
                    </textarea>
                </div>
            <?php endwhile; ?>
            <button type="submit">Enviar Examen</button>
        </form>
    <?php else: ?>
        <p>No hay preguntas disponibles para este examen.</p>
    <?php endif; ?>

    <a href="estudiante_dashboard.php">Volver al panel</a>
</body>
</html>
