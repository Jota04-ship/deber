<?php
// Activar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
include('../../includes/db.php');

// Asegurarse de que el profesor esté logueado
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener los parámetros de la URL
$estudiante_id = isset($_GET['estudiante_id']) ? $_GET['estudiante_id'] : null;
$clase_id = isset($_GET['clase_id']) ? $_GET['clase_id'] : null;
$examen_id = isset($_GET['examen_id']) ? $_GET['examen_id'] : null; // Si no lo pasas desde ver_examenes.php, asegúrate de pasarlo aquí

if (!$estudiante_id || !$clase_id || !$examen_id) {
    echo "Estudiante, clase o examen no especificados.";
    exit();
}

// Obtener el examen
$queryExamen = "
    SELECT e.titulo, e.descripcion, e.fecha
    FROM examenes e
    WHERE e.id = ?
";
$stmtExamen = $conn->prepare($queryExamen);
$stmtExamen->bind_param("i", $examen_id);
$stmtExamen->execute();
$resultExamen = $stmtExamen->get_result();
$examen = $resultExamen->fetch_assoc();

// Obtener las respuestas del estudiante con JOIN para obtener la pregunta
$queryRespuestas = "
    SELECT p.pregunta, r.respuesta
    FROM respuestas r
    JOIN preguntas p ON r.pregunta_id = p.id
    WHERE r.estudiante_id = ? AND r.examen_id = ?
";
$stmtRespuestas = $conn->prepare($queryRespuestas);
$stmtRespuestas->bind_param("ii", $estudiante_id, $examen_id);
$stmtRespuestas->execute();
$resultRespuestas = $stmtRespuestas->get_result();

// Verificar si se encontraron respuestas
if ($resultRespuestas->num_rows === 0) {
    echo "No se encontraron respuestas para este examen.";
    exit();
}

// Mostrar el examen y las respuestas del estudiante
echo "<h1>Examen: " . htmlspecialchars($examen['titulo'] ?? 'Título no disponible') . "</h1>";
echo "<p>Descripción: " . htmlspecialchars($examen['descripcion'] ?? 'Descripción no disponible') . "</p>";
echo "<p>Fecha: " . htmlspecialchars($examen['fecha'] ?? 'Fecha no disponible') . "</p>";

// Mostrar respuestas
echo "<table border='1'>
        <tr>
            <th>Pregunta</th>
            <th>Respuesta</th>
        </tr>";

while ($rowRespuesta = $resultRespuestas->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($rowRespuesta['pregunta']) . "</td>
            <td>" . htmlspecialchars($rowRespuesta['respuesta']) . "</td>
          </tr>";
}

echo "</table><br>";

// Formulario para calificar
$queryCalificacion = "
    SELECT calificacion
    FROM calificaciones_examenes
    WHERE examen_id = ? AND estudiante_id = ?
";
$stmtCalificacion = $conn->prepare($queryCalificacion);
$stmtCalificacion->bind_param("ii", $examen_id, $estudiante_id);
$stmtCalificacion->execute();
$resultCalificacion = $stmtCalificacion->get_result();
$calificacion = $resultCalificacion->num_rows > 0 ? $resultCalificacion->fetch_assoc()['calificacion'] : '';

echo "<form method='POST' action='calificar_examen.php'>
        <input type='hidden' name='examen_id' value='$examen_id'>
        <input type='hidden' name='estudiante_id' value='$estudiante_id'>
        <label for='calificacion'>Calificación:</label>
        <input type='number' name='calificacion' value='$calificacion' min='0' max='100' required>
        <button type='submit'>Guardar Calificación</button>
      </form>";

// Volver a la lista de exámenes
echo "<br><a href='ver_examenes.php?clase_id=$clase_id'>Volver a la lista de exámenes</a>";
?>
