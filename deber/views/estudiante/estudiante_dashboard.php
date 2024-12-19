<?php
session_start();
include '../../includes/db.php'; // Verifica que la ruta es correcta

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'estudiante') {
    header("Location: ../index.php"); // Redirige a login si no está logueado como estudiante
    exit();
}

// Obtener el ID del estudiante
$estudiante_id = $_SESSION['usuario_id'];

// Obtener los exámenes de las clases matriculadas
$queryExamenes = "
    SELECT e.id AS examen_id, e.titulo, e.fecha 
    FROM examenes e 
    JOIN clases c ON e.clase_id = c.id 
    JOIN matriculas mt ON mt.clase_id = c.id 
    WHERE mt.estudiante_id = ?";
    
$stmtExamenes = $conn->prepare($queryExamenes);
$stmtExamenes->bind_param("i", $estudiante_id);
$stmtExamenes->execute();
$resultExamenes = $stmtExamenes->get_result();

// Obtener los deberes asignados al estudiante
$queryDeberes = "
    SELECT d.id AS deber_id, d.titulo, d.fecha_entrega, d.archivo 
    FROM deberes d 
    JOIN clases c ON d.clase_id = c.id 
    JOIN matriculas mt ON mt.clase_id = c.id 
    WHERE mt.estudiante_id = ?";
    
$stmtDeberes = $conn->prepare($queryDeberes);
$stmtDeberes->bind_param("i", $estudiante_id);
$stmtDeberes->execute();
$resultDeberes = $stmtDeberes->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Estudiante</title>
</head>
<body>
    <h1>Panel del Estudiante</h1>

    <h2>Exámenes Disponibles</h2>
    <ul>
        <?php if (mysqli_num_rows($resultExamenes) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($resultExamenes)): ?>
                <li>
                    <strong><?php echo $row['titulo']; ?></strong> - Fecha: <?php echo $row['fecha']; ?>
                    <a href="resolver_examen.php?examen_id=<?php echo $row['examen_id']; ?>">Resolver Examen</a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No hay exámenes disponibles.</li>
        <?php endif; ?>
    </ul>

    <h2>Deberes Asignados</h2>
    <ul>
        <?php if (mysqli_num_rows($resultDeberes) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($resultDeberes)): ?>
                <li>
                    Título: <?php echo $row['titulo']; ?> - Fecha de Entrega: <?php echo $row['fecha_entrega']; ?>
                    <?php if ($row['archivo']): ?>
                        <a href="../uploads/<?php echo $row['archivo']; ?>" target="_blank">Ver Archivo</a>
                    <?php endif; ?>
                    <a href="entregas.php?deber_id=<?php echo $row['deber_id']; ?>">Enviar Deber</a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No tienes deberes asignados.</li>
        <?php endif; ?>
    </ul>

    <a href="../../logout.php">Cerrar sesión</a>
</body>
</html>
