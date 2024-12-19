<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'estudiante') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['clase_id'])) {
    $clase_id = $_GET['clase_id'];
    $estudiante_id = $_SESSION['usuario_id'];

    // Verificar si ya está matriculado en la clase
    $checkQuery = "SELECT * FROM matriculas WHERE estudiante_id = ? AND clase_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $estudiante_id, $clase_id);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        echo "Ya estás matriculado en esta clase.";
    } else {
        // Insertar la matriculación en la base de datos
        $query = "INSERT INTO matriculas (estudiante_id, clase_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $estudiante_id, $clase_id);

        if ($stmt->execute()) {
            echo "Matriculación exitosa.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Matriculación</title>
</head>
<body>
    <h1>Matriculación</h1>
    <p><a href="estudiante_dashboard.php">Volver al panel del estudiante</a></p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
