<?php
session_start();
include '../../includes/db.php';

// Verificar si el usuario es un estudiante
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'estudiante') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del deber (debería ser pasado como parámetro)
$deber_id = isset($_GET['deber_id']) ? $_GET['deber_id'] : null;

// Comprobar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_SESSION['usuario_id'];
    $archivoTmp = $_FILES['archivo']['tmp_name'];
    $nombreArchivo = basename($_FILES['archivo']['name']);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    // Mover el archivo subido a la carpeta correspondiente
    if (move_uploaded_file($archivoTmp, $rutaDestino)) {
        // Preparar la consulta para insertar la entrega
        $query = "INSERT INTO entregas (deber_id, estudiante_id, archivo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Comprobar si la preparación fue exitosa
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $conn->error);
        }

        // Vincular los parámetros (deber_id como entero, estudiante_id como entero, archivo como string)
        $stmt->bind_param("iis", $deber_id, $estudiante_id, $nombreArchivo);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Entrega realizada exitosamente.";
        } else {
            echo "Error al registrar la entrega: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error al subir el archivo.";
    }
}

// Obtener información del deber (opcional)
$queryDeber = "SELECT * FROM deberes WHERE id = ?";
$stmtDeber = $conn->prepare($queryDeber);
$stmtDeber->bind_param("i", $deber_id);
$stmtDeber->execute();
$resultDeber = $stmtDeber->get_result();
$deber = $resultDeber->fetch_assoc();

$stmtDeber->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrega de Deber</title>
</head>
<body>
    <h1>Entrega de Deber: <?php echo htmlspecialchars($deber['titulo']); ?></h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="archivo">Subir Archivo:</label>
        <input type="file" name="archivo" required><br>
        <button type="submit">Entregar</button>
    </form>

    <a href="estudiante_dashboard.php">Volver al panel del estudiante</a>
</body>
</html>
