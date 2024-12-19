<?php
session_start();
include '../../includes/db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Manejar la creación de la clase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia_id = $_POST['materia_id'];
    $profesor_id = $_POST['profesor_id'];
    $nombre_clase = $_POST['nombre_clase'];

    // Insertar la nueva clase
    $queryInsert = "INSERT INTO clases (materia_id, profesor_id, nombre_clase) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($queryInsert);
    $stmtInsert->bind_param("iis", $materia_id, $profesor_id, $nombre_clase);
    
    if ($stmtInsert->execute()) {
        echo "Clase creada exitosamente.";
    } else {
        echo "Error: " . $stmtInsert->error;
    }
    $stmtInsert->close();
}

// Obtener materias para el select
$queryMaterias = "SELECT * FROM materias";
$resultMaterias = mysqli_query($conn, $queryMaterias);

// Verificar si hay errores en la consulta
if (!$resultMaterias) {
    die("Error en la consulta de materias: " . mysqli_error($conn));
}

// Obtener profesores para el select
$queryProfesores = "SELECT * FROM usuarios WHERE tipo = 'profesor'";
$resultProfesores = mysqli_query($conn, $queryProfesores);

// Verificar si hay errores en la consulta
if (!$resultProfesores) {
    die("Error en la consulta de profesores: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Clase</title>
</head>
<body>
    <h1>Crear Nueva Clase</h1>
    <form method="POST" action="">
        Materia:
        <select name="materia_id" required>
            <option value="">Selecciona una materia</option>
            <?php 
            if (mysqli_num_rows($resultMaterias) > 0) {
                while ($row = mysqli_fetch_assoc($resultMaterias)) {
                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                }
            } else {
                echo '<option value="">No hay materias disponibles</option>';
            }
            ?>
        </select><br>

        Profesor:
        <select name="profesor_id" required>
            <option value="">Selecciona un profesor</option>
            <?php 
            if (mysqli_num_rows($resultProfesores) > 0) {
                while ($row = mysqli_fetch_assoc($resultProfesores)) {
                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                }
            } else {
                echo '<option value="">No hay profesores disponibles</option>';
            }
            ?>
        </select><br>

        Nombre de la Clase:
        <input type="text" name="nombre_clase" placeholder="Ej. 1A Software" required><br>

        <button type="submit">Crear Clase</button>
    </form>

    <a href="admin_dashboard.php">Volver al panel del administrador</a>
</body>
</html>
