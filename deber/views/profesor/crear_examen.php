<?php
session_start();
include '../../includes/db.php';

// Verificar si el usuario es un profesor
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Manejo del formulario de creación de examen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $clase_id = $_POST['clase_id'];

    // Verificar si el título ya existe en la base de datos
    $queryVerificarTitulo = "SELECT COUNT(*) AS total FROM examenes WHERE titulo = ?";
    $stmtVerificarTitulo = $conn->prepare($queryVerificarTitulo);
    $stmtVerificarTitulo->bind_param("s", $nombre); // "s" es el tipo de parámetro (string)
    $stmtVerificarTitulo->execute();
    $resultVerificarTitulo = $stmtVerificarTitulo->get_result();
    $row = $resultVerificarTitulo->fetch_assoc();
    
    if ($row['total'] > 0) {
        // Si el título ya existe, mostramos un mensaje de error
        echo "Error: Ya existe un examen con el título '$nombre'. Por favor, elige otro título.";
    } else {
        // Insertar examen en la base de datos si no existe un examen con el mismo título
        $query = "INSERT INTO examenes (titulo, fecha, clase_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $nombre, $fecha, $clase_id);
        
        if ($stmt->execute()) {
            header("Location: agregar_preguntas.php?examen_id=" . $conn->insert_id);
            exit();
        } else {
            echo "Error al crear el examen: " . $stmt->error;
        }
        $stmt->close();
    }
    $stmtVerificarTitulo->close();
}

// Obtener las clases del profesor
$queryClases = "SELECT c.id AS clase_id, c.nombre_clase AS clase_nombre FROM clases c WHERE c.profesor_id = ?";
$stmtClases = $conn->prepare($queryClases);
$stmtClases->bind_param("i", $_SESSION['usuario_id']);
$stmtClases->execute();
$resultClases = $stmtClases->get_result();

// Verificar si se encontraron clases
if ($resultClases->num_rows === 0) {
    echo "No tienes clases asignadas.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Examen</title>
</head>
<body>
    <h1>Crear Nuevo Examen</h1>
    <form method="POST" action="">
        <label for="nombre">Nombre del Examen:</label>
        <input type="text" name="nombre" id="nombre" required>
        
        <label for="fecha">Fecha del Examen:</label>
        <input type="date" name="fecha" id="fecha" required>
        
        <label for="clase_id">Selecciona una Clase:</label>
        <select name="clase_id" id="clase_id" required>
            <option value="">Seleccione una clase</option>
            <?php while ($row = $resultClases->fetch_assoc()): ?>
                <option value="<?php echo $row['clase_id']; ?>"><?php echo $row['clase_nombre']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit">Crear Examen</button>
    </form>

    <a href="profesor_dashboard.php">Volver al panel</a>
</body>
</html>
