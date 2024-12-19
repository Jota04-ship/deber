<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $clase_id = $_GET['id'];

    // Obtener la clase a editar
    $queryClase = "SELECT * FROM clases WHERE id = ?";
    $stmtClase = $conn->prepare($queryClase);
    $stmtClase->bind_param("i", $clase_id);
    $stmtClase->execute();
    $resultClase = $stmtClase->get_result();
    $clase = $resultClase->fetch_assoc();
    $stmtClase->close();

    // Procesar el formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $materia_id = $_POST['materia_id'];
        $profesor_id = $_POST['profesor_id'];
        $nombre_clase = $_POST['nombre_clase'];

        $queryUpdate = "UPDATE clases SET materia_id = ?, profesor_id = ?, nombre_clase = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bind_param("iisi", $materia_id, $profesor_id, $nombre_clase, $clase_id);
        
        if ($stmtUpdate->execute()) {
            echo "Clase actualizada exitosamente.";
        } else {
            echo "Error: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    }
}

// Obtener materias para el select
$queryMaterias = "SELECT * FROM materias";
$resultMaterias = mysqli_query($conn, $queryMaterias);

// Obtener profesores para el select
$queryProfesores = "SELECT * FROM usuarios WHERE tipo = 'profesor'";
$resultProfesores = mysqli_query($conn, $queryProfesores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Clase</title>
</head>
<body>
    <h1>Editar Clase</h1>
    <form method="POST" action="">
        Materia:
        <select name="materia_id" required>
            <?php while ($row = mysqli_fetch_assoc($resultMaterias)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $clase['materia_id']) ? 'selected' : ''; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        Profesor:
        <select name="profesor_id" required>
            <?php while ($row = mysqli_fetch_assoc($resultProfesores)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $clase['profesor_id']) ? 'selected' : ''; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        Nombre de la Clase:
        <input type="text" name="nombre_clase" value="<?php echo $clase['nombre_clase']; ?>" required><br>

        <button type="submit">Actualizar Clase</button>
    </form>

    <a href="ver_clases.php">Volver a la lista de clases</a>
</body>
</html>
