<?php
session_start();
include '../../includes/db.php';  // Asegúrate de que la ruta sea correcta

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

$clase_id = $_GET['clase_id'];

// Obtener los estudiantes de la clase (suponiendo que hay relación directa por clase_id)
$queryEstudiantes = "
    SELECT u.id, u.nombre 
    FROM usuarios u
    JOIN clases c ON c.profesor_id = ? 
    WHERE u.tipo = 'estudiante' AND c.id = ?";
$stmtEstudiantes = $conn->prepare($queryEstudiantes);
$stmtEstudiantes->bind_param("ii", $_SESSION['usuario_id'], $clase_id);
$stmtEstudiantes->execute();
$resultEstudiantes = $stmtEstudiantes->get_result();

// Obtener los deberes de la clase
$queryDeberes = "
    SELECT id, titulo, archivo, fecha_entrega
    FROM deberes
    WHERE clase_id = ?";
$stmtDeberes = $conn->prepare($queryDeberes);
$stmtDeberes->bind_param("i", $clase_id);
$stmtDeberes->execute();
$resultDeberes = $stmtDeberes->get_result();

// Manejar el envío de calificaciones y comentarios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['calificaciones'] as $deber_id => $calificaciones) {
        foreach ($calificaciones as $estudiante_id => $data) {
            $calificacion = $data['calificacion'];
            $comentario = $data['comentario'];

            // Verificamos si el estudiante ha entregado el deber
            $queryEntrega = "
                SELECT cd.id, d.archivo 
                FROM calificaciones_deber cd 
                JOIN deberes d ON cd.deber_id = d.id 
                WHERE cd.deber_id = ? AND cd.estudiante_id = ? AND d.clase_id = ?";
            $stmtEntrega = $conn->prepare($queryEntrega);
            $stmtEntrega->bind_param("iii", $deber_id, $estudiante_id, $clase_id);
            $stmtEntrega->execute();
            $resultEntrega = $stmtEntrega->get_result();

            // Si existe una entrega, se puede calificar
            if ($resultEntrega->num_rows > 0) {
                // Insertar o actualizar calificación para cada estudiante en cada deber
                $queryInsert = "
                    INSERT INTO calificaciones_deber (deber_id, estudiante_id, calificacion, comentario) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE calificacion = ?, comentario = ?";
                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->bind_param("iiisss", $deber_id, $estudiante_id, $calificacion, $comentario, $calificacion, $comentario);
                $stmtInsert->execute();
            } else {
                // Si no ha entregado el deber, aún se puede calificar
                $queryInsert = "
                    INSERT INTO calificaciones_deber (deber_id, estudiante_id, calificacion, comentario) 
                    VALUES (?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->bind_param("iiis", $deber_id, $estudiante_id, $calificacion, $comentario);
                $stmtInsert->execute();
            }
        }
    }
    header("Location: ver_deberes.php?clase_id=$clase_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Deberes y Calificar</title>
</head>
<body>
    <h1>Deberes de la Clase</h1>

    <form method="POST" action="">
        <table border="1">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Deber</th>
                    <th>Archivo Subido</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while ($estudiante = $resultEstudiantes->fetch_assoc()):
                    // Para cada estudiante, obtenemos sus entregas de los deberes
                    while ($deber = $resultDeberes->fetch_assoc()):
                        // Verificamos si el estudiante ha entregado el deber
                        $queryEntrega = "
                            SELECT cd.id, d.archivo, cd.calificacion, cd.comentario 
                            FROM calificaciones_deber cd 
                            JOIN deberes d ON cd.deber_id = d.id 
                            WHERE cd.deber_id = ? AND cd.estudiante_id = ? AND d.clase_id = ?";
                        $stmtEntrega = $conn->prepare($queryEntrega);
                        $stmtEntrega->bind_param("iii", $deber['id'], $estudiante['id'], $clase_id);
                        $stmtEntrega->execute();
                        $resultEntrega = $stmtEntrega->get_result();
                        
                        // Si el estudiante no ha entregado, resultEntrega estará vacío
                        if ($resultEntrega->num_rows > 0) {
                            $entrega = $resultEntrega->fetch_assoc();
                            $archivo = $entrega['archivo'] ?: 'Deber no entregado';
                            $calificacion = $entrega['calificacion'];
                            $comentario = $entrega['comentario'];
                        } else {
                            $archivo = 'Deber no entregado';
                            $calificacion = '';
                            $comentario = '';
                        }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($estudiante['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($deber['titulo']); ?></td>
                        <td>
                            <?php 
                            if ($archivo !== 'Deber no entregado') {
                                echo '<a href="ruta/al/archivo/' . htmlspecialchars($archivo) . '" target="_blank">Ver archivo</a>';
                            } else {
                                echo $archivo;  // Mensaje si no hay entrega
                            }
                            ?>
                        </td>
                        <td>
                            <input type="number" name="calificaciones[<?php echo $deber['id']; ?>][<?php echo $estudiante['id']; ?>][calificacion]" 
                                   value="<?php echo $calificacion; ?>" min="0" max="100" required>
                        </td>
                        <td>
                            <textarea name="calificaciones[<?php echo $deber['id']; ?>][<?php echo $estudiante['id']; ?>][comentario]" 
                                      rows="4" cols="50"><?php echo $comentario; ?></textarea>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Guardar Calificaciones</button>
    </form>
    <a href="profesor_dashboard.php">Volver al panel</a>
</body>
</html>
