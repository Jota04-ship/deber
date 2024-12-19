<?php
session_start();
include '../../includes/db.php';

// Verificar si el usuario está logueado y es profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la clase desde la URL
$clase_id = isset($_GET['clase_id']) ? $_GET['clase_id'] : null;

if (!$clase_id) {
    echo "Clase no especificada.";
    exit();
}

// Obtener los estudiantes en esta clase
$queryEstudiantes = "
    SELECT u.id AS estudiante_id, u.nombre, c.nombre_clase 
    FROM usuarios u
    JOIN matriculas m ON m.estudiante_id = u.id
    JOIN clases c ON c.id = m.clase_id
    WHERE m.clase_id = ?
";
$stmtEstudiantes = $conn->prepare($queryEstudiantes);
$stmtEstudiantes->bind_param("i", $clase_id);
$stmtEstudiantes->execute();
$resultEstudiantes = $stmtEstudiantes->get_result();

// Verificar si hay estudiantes
if ($resultEstudiantes->num_rows === 0) {
    echo "No hay estudiantes en esta clase.";
    exit();
}

// Obtener las calificaciones de los exámenes y deberes
$queryCalificacionesExamenes = "
    SELECT estudiante_id, examen_id, calificacion 
    FROM calificaciones_examenes 
    WHERE examen_id IN (SELECT id FROM examenes WHERE clase_id = ?)
";
$stmtCalificacionesExamenes = $conn->prepare($queryCalificacionesExamenes);
$stmtCalificacionesExamenes->bind_param("i", $clase_id);
$stmtCalificacionesExamenes->execute();
$resultExamenesCalificaciones = $stmtCalificacionesExamenes->get_result();

$queryCalificacionesDeberes = "
    SELECT estudiante_id, deber_id, calificacion 
    FROM calificaciones_deber 
    WHERE deber_id IN (SELECT id FROM deberes WHERE clase_id = ?)
";
$stmtCalificacionesDeberes = $conn->prepare($queryCalificacionesDeberes);
$stmtCalificacionesDeberes->bind_param("i", $clase_id);
$stmtCalificacionesDeberes->execute();
$resultDeberesCalificaciones = $stmtCalificacionesDeberes->get_result();

// Función para calcular el promedio final
function calcularPromedioFinal($deberes, $examenes) {
    $promedioDeberes = !empty($deberes) ? array_sum($deberes) / count($deberes) : 0;
    $promedioExamenes = !empty($examenes) ? array_sum($examenes) / count($examenes) : 0;
    return ($promedioDeberes * 0.6) + ($promedioExamenes * 0.4);  // 60% deberes, 40% exámenes
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificaciones de Estudiantes</title>
</head>
<body>
    <h1>Calificaciones de los estudiantes para la clase</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Clase</th>
                <th>Deberes</th>
                <th>Exámenes</th>
                <th>Promedio Final</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Obtener los datos de los estudiantes
            while ($estudiante = $resultEstudiantes->fetch_assoc()) {
                $estudiante_id = $estudiante['estudiante_id'];
                $nombre_estudiante = $estudiante['nombre'];
                $clase_nombre = $estudiante['nombre_clase'];

                // Obtener las calificaciones de deberes
                $deberes = [];
                while ($deber = $resultDeberesCalificaciones->fetch_assoc()) {
                    if ($deber['estudiante_id'] == $estudiante_id) {
                        $deberes[] = $deber['calificacion'];
                    }
                }

                // Obtener las calificaciones de exámenes
                $examenes = [];
                while ($examen = $resultExamenesCalificaciones->fetch_assoc()) {
                    if ($examen['estudiante_id'] == $estudiante_id) {
                        $examenes[] = $examen['calificacion'];
                    }
                }

                // Calcular el promedio final
                $promedioFinal = calcularPromedioFinal($deberes, $examenes);
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($nombre_estudiante); ?></td>
                    <td><?php echo htmlspecialchars($clase_nombre); ?></td>
                    <td><?php echo !empty($deberes) ? implode(', ', $deberes) : 'No asignado'; ?></td>
                    <td><?php echo !empty($examenes) ? implode(', ', $examenes) : 'No asignado'; ?></td>
                    <td><?php echo number_format($promedioFinal, 2); ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <a href="profesor_dashboard.php">Volver al panel</a>
</body>
</html>
