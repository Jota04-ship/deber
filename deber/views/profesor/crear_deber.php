<?php
session_start();

// Asegúrate de que la ruta a db.php sea correcta
include '../../includes/db.php'; 

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Manejo de selección de clase
$clase_id = isset($_GET['clase_id']) ? $_GET['clase_id'] : null;

// Obtener la materia de la clase seleccionada
$query = "SELECT m.id AS materia_id, m.nombre FROM clases c 
          JOIN materias m ON c.materia_id = m.id 
          WHERE c.id = '$clase_id'";
$result = mysqli_query($conn, $query);
$materia = mysqli_fetch_assoc($result);

// Verificar si la materia existe
if (!$materia) {
    echo "Clase no encontrada.";
    exit();
}

// Manejo del formulario de creación de deber
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_entrega = $_POST['fecha_entrega'];

    // Manejo del archivo subido
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $nombreArchivo = basename($_FILES['archivo']['name']);
        
        // Limpiar el nombre del archivo (eliminar caracteres especiales)
        $nombreArchivo = preg_replace("/[^a-zA-Z0-9\.]/", "_", $nombreArchivo);

        // Usar ruta absoluta para asegurar que se encuentra en el directorio correcto
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $nombreArchivo;

        // Verificar si la carpeta uploads existe
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/uploads", 0777, true);  // Crear la carpeta si no existe
        }

        // Mover el archivo a la carpeta uploads
        if (move_uploaded_file($archivoTmp, $rutaDestino)) {
            // Inserción en la base de datos
            $query = "INSERT INTO deberes (titulo, descripcion, fecha_entrega, clase_id, archivo) VALUES ('$titulo', '$descripcion', '$fecha_entrega', '$clase_id', '$nombreArchivo')";
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
