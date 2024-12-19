<?php
// Incluir la conexión a la base de datos
include('../../includes/db.php');

// Asegurarse de que el profesor esté logueado
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la clase desde la URL
$clase_id = isset($_GET['clase_id']) ? $_GET['clase_id'] : null;
if (!$clase_id) {
    echo "Clase no especificada.";
    exit();
}

// Obtener el nombre de la clase
$queryClase = "SELECT nombre_clase FROM clases WHERE id = ?";
$stmtClase = $conn->prepare($queryClase);
$stmtClase->bind_param("i", $clase_id);
$stmtClase->execute();
$resultClase = $stmtClase->get_result();
$clase = $resultClase->fetch_assoc();

if (!$clase) {
    echo "Clase no encontrada.";
    exit();
}

// Obtener los exámenes disponibles para esta clase
$queryExamenes = "
    SELECT e.id, e.titulo, e.fecha, e.descripcion
    FROM examenes e
    WHERE e.clase_id = ?
";
$stmtExamenes = $conn->prepare($queryExamenes);
$stmtExamenes->bind_param("i", $clase_id);
$stmtExamenes->execute();
$resultExamenes = $stmtExamenes->get_result();

// Verificar si se encontraron exámenes
if ($resultExamenes->num_rows === 0) {
    echo "No hay exámenes disponibles para esta clase.";
    exit();
}

// Mostrar los exámenes disponibles
echo "<h1>Exámenes disponibles para la clase: " . htmlspecialchars($clase['nombre_clase']) . "</h1>";

while ($rowExamen = $resultExamenes->fetch_assoc()) {
    $examen_id = $rowExamen['id'];
    $titulo_examen = htmlspecialchars($rowExamen['titulo']);
    $fecha_examen = htmlspecialchars($rowExamen['fecha']);
    $descripcion_examen = htmlspecialchars($rowExamen['descripcion']);
    
    echo "<h2>Examen: " . $titulo_examen . "</h2>";
    echo "<p>Descripción: " . $descripcion_examen . "</p>";
    echo "<p>Fecha: " . $fecha_examen . "</p>";

    // Obtener los estudiantes que han tomado este examen en la clase seleccionada (sin duplicados)
    $queryEstudiantes = "
        SELECT DISTINCT u.id AS estudiante_id, u.nombre, m.clase_id, r.respuesta
        FROM usuarios u
        JOIN matriculas m ON m.estudiante_id = u.id
        LEFT JOIN respuestas r ON r.estudiante_id = u.id AND r.examen_id = ?
        WHERE m.clase_id = ?
    ";
    $stmtEstudiantes = $conn->prepare($queryEstudiantes);
    $stmtEstudiantes->bind_param("ii", $examen_id, $clase_id);
    $stmtEstudiantes->execute();
    $resultEstudiantes = $stmtEstudiantes->get_result();

    // Verificar si se encontraron estudiantes
    if ($resultEstudiantes->num_rows === 0) {
        echo "<p>No hay estudiantes que hayan tomado este examen en esta clase.</p>";
    } else {
        // Mostrar tabla de estudiantes con su clase
        echo "<table border='1' style='width: 100%; margin-bottom: 20px;'>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Estado</th>
                        <th>Clase</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>";

        // Mostrar cada estudiante, su estado y la clase
        while ($student = $resultEstudiantes->fetch_assoc()) {
            $estudiante_id = $student['estudiante_id'];
            $nombre_estudiante = htmlspecialchars($student['nombre']);
            $respuesta = $student['respuesta'] ? "Respondido" : "No respondido";
            
            // Obtener el nombre de la clase del estudiante
            $queryNombreClase = "SELECT nombre_clase FROM clases WHERE id = ?";
            $stmtClaseEstudiante = $conn->prepare($queryNombreClase);
            $stmtClaseEstudiante->bind_param("i", $student['clase_id']);
            $stmtClaseEstudiante->execute();
            $resultClaseEstudiante = $stmtClaseEstudiante->get_result();
            $claseEstudiante = $resultClaseEstudiante->fetch_assoc();
            $nombre_clase_estudiante = $claseEstudiante ? $claseEstudiante['nombre_clase'] : 'Clase no encontrada';

            // Mostrar cada estudiante y su clase
            echo "<tr>
                    <td>" . $nombre_estudiante . "</td>
                    <td>" . $respuesta . "</td>
                    <td>" . $nombre_clase_estudiante . "</td>
                    <td><a href='calificar_examen.php?examen_id=$examen_id&estudiante_id=$estudiante_id&clase_id=$clase_id'>Calificar examen</a></td>
                  </tr>";
        }

        echo "</tbody></table>";
    }
}

// Volver al panel de control del profesor
echo "<br><a href='profesor_dashboard.php'>Volver al panel</a>";
?>
