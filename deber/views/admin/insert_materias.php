<?php
session_start();
include '../../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php"); // Redirige si no es administrador
    exit();
}

$error = ""; // Variable para almacenar errores
$exito = ""; // Variable para almacenar mensaje de éxito

// Lógica para crear una nueva materia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']); // Eliminar espacios en blanco

    // Verifica si la materia ya existe
    $query = "SELECT * FROM materias WHERE nombre = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nombre); // Vincula el parámetro
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "La materia '$nombre' ya existe.";
    } else {
        // Inserción en la base de datos
        $query = "INSERT INTO materias (nombre) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nombre); // Vincula el parámetro
        if ($stmt->execute()) {
            $exito = "Materia creada exitosamente."; // Mensaje de éxito
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Materia</title>
    <style>
        /* Aquí se incluyen los mismos estilos del primer código */
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

        /* Barra lateral */
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

        /* Contenido principal */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
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

        main {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        /* Estilo de los formularios y mensajes de error */
        .form-container {
            margin: 20px 0;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #960019;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #b22223;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-top: 15px;
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
            <h1>Crear Nueva Materia</h1>
        </header>
        <main>
            <!-- Mensaje de error si existe alguno -->
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <!-- Mensaje de éxito, aparece después de la creación exitosa -->
            <?php if ($exito): ?>
                <p class="success"><?php echo $exito; ?></p>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <label for="nombre">Nombre de la Materia:</label>
                    <input type="text" name="nombre" id="nombre" required>
                    <button type="submit">Crear Materia</button>
                </form>
            </div>

            <a href="admin_dashboard.php">Volver al Panel Administrador</a>
        </main>
    </div>
</body>
</html>

