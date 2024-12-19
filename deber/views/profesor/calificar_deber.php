<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

$deber_id = isset($_GET['deber_id']) ? $_GET['deber_id'] : null;
$clase_id = isset($_GET['clase_id']) ? $_GET['clase_id'] : null;

if (!$deber_id || !$clase_id) {
    echo "Deber o clase no especificados.";
    exit();
}

// Obtener estudiantes que realizaron el deber
$queryEstudiantes = "
    SELECT u.id, u.nombre, d.calificacion 
    FROM usuarios u 
    LEFT JOIN calificaciones_deber d ON d.estudiante_id = u.id AND d.deber_id = ? 
    JOIN matriculas m ON m.estudiante_id = u.id 
    WHERE m.clase_id = ?";
$stmtEstudiantes = $conn->prepare($queryEstudiantes);
$stmtEstudiantes->bind_param("ii", $deber_id, $clase_id);
$stmtEstudiantes->execute();
$resultEstudiantes = $stmtEstudiantes->get_result();

// Manejar el envío de calificaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['calificaciones'] as $estudiante_id => $calificacion) {
        // Insertar o actualizar calificación
        $queryInsert = "
            INSERT INTO calificaciones_deber (deber_id, estudiante_id, calificacion) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE calificacion = ?";
        $stmtInsert = $conn->prepare($queryInsert);
        
        if (!$stmtInsert) {
            echo "Error en la preparación de la consulta de inserción: " . $conn->error;
            exit();
        }

        $stmtInsert->bind_param("iiis", $deber_id, $estudiante_id, $calificacion, $calificacion);
        $stmtInsert->execute();
    }
    calcularPromedio($clase_id);
    header("Location: ver_deberes.php?clase_id=$clase_id");
    exit();
}

function calcularPromedio($clase_id) {
    global $conn;
    
    // Calcular promedios
    $query = "
        SELECT estudiante_id, 
               AVG(calificacion) * 0.7 AS promedio_deber
        FROM calificaciones_deber 
        WHERE estudiante_id IN (SELECT estudiante_id FROM matriculas WHERE clase_id = ?)
        GROUP BY estudiante_id";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clase_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $estudiante_id = $row['estudiante_id'];
        $promedio_deber = $row['promedio_deber'] ?? 0;

        $queryExamenes = "
            SELECT AVG(calificacion) * 0.3 AS promedio_examen
            FROM calificaciones 
            WHERE estudiante_id = ?";
        $stmtExamenes = $conn->prepare($queryExamenes);
        $stmtExamenes->bind_param("i", $estudiante_id);
        $stmtExamenes->execute();
        $resultExamenes = $stmtExamenes->get_result();
        $promedio_examen = $resultExamenes->fetch_assoc()['promedio_examen'] ?? 0;

        $promedio_final = $promedio_deber + $promedio_examen;
        
        // Guardar en calificaciones finales
        $queryFinal = "
            INSERT INTO calificaciones_finales (estudiante_id, promedio) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE promedio = ?";
        $stmtFinal = $conn->prepare($queryFinal);
        $stmtFinal->bind_param("idd", $estudiante_id, $promedio_final, $promedio_final);
        $stmtFinal->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar Deber</title>
</head>
<body>
    <h1>Calificar Deber</h1>
    <form method="POST" action="">
        <table>
            <tr>
                <th>Estudiante</th>
                <th>Calificación</th>
            </tr>
            <?php while ($row = $resultEstudiantes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td>
                        <input type="number" name="calificaciones[<?php echo $row['id']; ?>]" 
                               value="<?php echo $row['calificacion'] ?: 0; ?>" min="0" max="100" step="1" required>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button type="submit">Guardar Calificaciones</button>
    </form>
    <a href="ver_deberes.php?clase_id=<?php echo $clase_id; ?>">Volver a Deberes</a>
</body>
</html>
