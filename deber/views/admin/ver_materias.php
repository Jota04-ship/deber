<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

$error = ""; // Variable para almacenar errores
$exito = ""; // Variable para almacenar mensaje de éxito

// Eliminar materia si se solicita
if (isset($_GET['delete_id'])) {
    $materia_id = $_GET['delete_id'];
    $query = "DELETE FROM materias WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $materia_id);
    if ($stmt->execute()) {
        $exito = "Materia eliminada exitosamente."; // Mensaje de éxito
    } else {
        $error = "Error: " . $stmt->error; // Mensaje de error
    }
    $stmt->close();
}

// Obtener todas las materias
$query = "SELECT * FROM materias";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Materias</title>
    <style>
        /* Estilos generales */
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

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        ul {
            list-style: none;
            padding-left: 0;
        }

        li {
            margin-bottom: 10px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li a {
            text-decoration: none;
            color: white;
            background-color: #960019;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        li a:hover {
            background-color: #b22223;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .btn-container button {
            padding: 5px 10px;
            background-color: #960019;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-container button:hover {
            background-color: #b22223;
        }

        .btn-container a {
            text-decoration: none;
            color: white;
            background-color: #960019;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn-container a:hover {
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
            <h1>Lista de Materias</h1>
        </header>

        <!-- Mostrar el mensaje de error o éxito -->
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php elseif ($exito): ?>
            <div class="message success"><?php echo $exito; ?></div>
        <?php endif; ?>

        <!-- Lista de materias -->
        <ul>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <li>
                    <?php echo $row['nombre']; ?>
                    <div class="btn-container">
                        <!-- Botones Editar y Eliminar con diseño consistente -->
                        <a href="edit_materia.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta materia?');">Eliminar</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <a href="admin_dashboard.php" class="btn-container">Volver al panel del administrador</a>
    </div>
</body>
</html>
