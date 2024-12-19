<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la materia desde la URL
$materia_id = $_GET['id'];

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);

    // Actualizar la materia
    $query = "UPDATE materias SET nombre = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nombre, $materia_id); // Vincula el parámetro
    if ($stmt->execute()) {
        echo "Materia actualizada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    mysqli_close($conn);
    header("Location: ver_materias.php"); // Redirigir después de la actualización
    exit();
}

// Obtener la materia actual
$query = "SELECT * FROM materias WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $materia_id);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();

if (!$materia) {
    echo "Materia no encontrada.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <style>
        /* Estilos generales para mantener el diseño consistente */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f4f4f9;
        }

        .sidebar {
            width: 250px;
            background-color: #960019;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            width: 100%;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            padding: 15px 20px;
            display: block;
            width: 100%;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #b22223;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        header {
            background-color: #ecf0f1;
            padding: 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 24px;
            color: #960019;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #960019;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background-color: #b22223;
        }
    </style>
</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar">
        <h2>Panel Administrador</h2>
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Gestión de Docentes</a></li>
            <li><a href="#">Gestión de Estudiantes</a></li>
            <li><a href="#">Gestión de Cursos</a></li>
            <li><a href="#">Gestión de Asignaturas</a></li>
            <li><a href="#">Reportes</a></li>
            <li><a href="#">Configuración</a></li>
            <li><a href="log.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <header>
            <h1>Editar Materia</h1>
        </header>

        <!-- Formulario de edición de materia -->
        <div class="form-container">
            <form method="POST" action="">
                <label for="nombre">Nombre de la Materia:</label>
                <input type="text" name="nombre" value="<?php echo $materia['nombre']; ?>" required>
                <button type="submit">Actualizar Materia</button>
            </form>
            <a href="ver_materias.php" class="btn-container">Volver a la lista de materias</a>
        </div>
    </div>
</body>
</html>
