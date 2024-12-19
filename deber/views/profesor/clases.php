<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la materia desde la URL
$materia_id = $_GET['materia_id'];

// Obtener la materia
$query = "SELECT m.id AS materia_id, m.nombre FROM materias m 
          WHERE m.id = '$materia_id'";
$result = mysqli_query($conn, $query);
$materia = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_entrega = $_POST['fecha_entrega'];
    
    // Manejo del archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $nombreArchivo = basename($_FILES['archivo']['name']);
        $rutaDestino = "../uploads/" . $nombreArchivo;

        // Mover el archivo a la carpeta uploads
        if (move_uploaded_file($archivoTmp, $rutaDestino)) {
            // Inserción en la base de datos
            $query = "INSERT INTO deberes (titulo, descripcion, fecha_entrega, materia_id, archivo) VALUES ('$titulo', '$descripcion', '$fecha_entrega', '$materia_id', '$nombreArchivo')";
            if (mysqli_query($conn, $query)) {
                echo "Deber creado exitosamente.";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "No se subió ningún archivo o hubo un error.";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Deber</title>
</head>
<body>
    <h1>Crear Nuevo Deber para <?php echo $materia['nombre']; ?></h1>
    <form method="POST" action="" enctype="multipart/form-data">
        Título: <input type="text" name="titulo" required><br>
        Descripción: <textarea name="descripcion" required></textarea><br>
        Fecha de Entrega: <input type="datetime-local" name="fecha_entrega" required><br>
        Subir Archivo: <input type="file" name="archivo" required><br>
        <button type="submit">Crear Deber</button>
    </form>
    <a href="profesor_dashboard.php">Volver al panel del profesor</a>
</body>
</html>
