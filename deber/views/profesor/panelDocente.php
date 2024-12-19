<?php
session_start();
include '../../includes/db.php';  // Incluir la conexión a la base de datos

// Verificar si el docente está logueado
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del profesor desde la sesión
$profesor_id = $_SESSION['usuario_id'];

// Consultar el total de cursos que tiene el profesor
$query_cursos = "SELECT COUNT(*) AS total_cursos FROM clases WHERE profesor_id = '$profesor_id'";
$result_cursos = mysqli_query($conn, $query_cursos);
$total_cursos = $result_cursos ? mysqli_fetch_assoc($result_cursos)['total_cursos'] : 0;

// Consultar las clases que enseña el profesor para contar estudiantes
$query_clases = "SELECT id FROM clases WHERE profesor_id = '$profesor_id'";
$result_clases = mysqli_query($conn, $query_clases);
$total_estudiantes = 0;
if ($result_clases) {
    while ($row_clase = mysqli_fetch_assoc($result_clases)) {
        $clase_id = $row_clase['id'];
        $query_estudiantes = "SELECT COUNT(*) AS total_estudiantes FROM matriculas WHERE clase_id = '$clase_id'";
        $result_estudiantes = mysqli_query($conn, $query_estudiantes);
        if ($result_estudiantes) {
            $total_estudiantes += mysqli_fetch_assoc($result_estudiantes)['total_estudiantes'];
        }
    }
}

// Consultar el total de tareas recibidas (pendientes de revisión)
$query_tareas = "SELECT COUNT(*) AS total_tareas FROM deberes WHERE clase_id IN (SELECT clase_id FROM clases WHERE profesor_id = '$profesor_id') AND estado != 'completada'";
$result_tareas = mysqli_query($conn, $query_tareas);
$total_tareas = $result_tareas ? mysqli_fetch_assoc($result_tareas)['total_tareas'] : 0;

// Consultar las clases y materias que enseña el profesor
$query_materias_clases = "
    SELECT materias.nombre AS materia_nombre, clases.nombre_clase 
    FROM clases
    JOIN materias ON clases.materia_id = materias.id
    WHERE clases.profesor_id = '$profesor_id'
";
$result_materias_clases = mysqli_query($conn, $query_materias_clases);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente</title>
    <style>
        /* Estilos generales */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; height: 100vh; background-color: #f4f4f9; }
        .sidebar { width: 250px; background-color: #960019; color: white; display: flex; flex-direction: column; align-items: center; padding-top: 20px; position: fixed; height: 100%; }
        .sidebar h2 { margin-bottom: 20px; }
        .sidebar ul { list-style: none; width: 100%; }
        .sidebar ul li { width: 100%; }
        .sidebar ul li a { text-decoration: none; color: white; padding: 15px 20px; display: block; width: 100%; transition: background 0.3s; }
        .sidebar ul li a:hover { background-color: #b22223; }
        .content { margin-left: 250px; flex: 1; display: flex; flex-direction: column; }
        header { background-color: #ecf0f1; padding: 20px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); }
        header h1 { font-size: 24px; color: #960019; }
        main { padding: 20px; flex: 1; overflow-y: auto; }
        .section { display: none; }
        .section.active { display: block; }
        .card-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); text-align: center; cursor: pointer; transition: transform 0.3s, box-shadow 0.3s; }
        .card h3 { font-size: 18px; color: #960019; margin-bottom: 10px; }
        .card:hover { transform: scale(1.05); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }
        .activities-list { margin-top: 20px; }
        .activity { background-color: #f4f4f9; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .activity button { margin-left: 10px; background-color: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px; }
        .activity button:hover { background-color: #c0392b; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Panel Docente</h2>
        <ul>
            <li><a href="#" onclick="showSection('dashboard')">Inicio</a></li>
            <li><a href="#" onclick="showSection('courses')">Mis Cursos</a></li>
            <li><a href="#" onclick="showSection('students')">Estudiantes</a></li>
            <li><a href="#" onclick="showSection('materials')">Materiales</a></li>
            <li><a href="#" onclick="showSection('configuration')">Configuración</a></li>
            <li><a href="../../logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h1>Bienvenido, Profesor</h1>
        </header>
        <main> 
            <!-- Sección de Inicio -->
            <div id="dashboard" class="section active">
                <h2>Datos Generales</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Total de Cursos</h3>
                        <p>Actualmente tiene <?php echo $total_cursos; ?> cursos activos</p>
                    </div>
                    <div class="card">
                        <h3>Estudiantes</h3>
                        <p>Por todas las materias dadas hay <?php echo $total_estudiantes; ?> estudiantes inscritos en total.</p>
                    </div>
                    <div class="card">
                        <h3>Tareas Recibidas</h3>
                        <p><?php echo $total_tareas; ?> tareas pendientes de revisión.</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Mis Cursos -->
            <div id="courses" class="section">
                <h2>Mis Cursos</h2>
                <div class="card-container">
                    <?php
                    if ($result_materias_clases && mysqli_num_rows($result_materias_clases) > 0) {
                        while ($row = mysqli_fetch_assoc($result_materias_clases)) {
                            echo "<div class='card'>";
                            echo "<h3>" . htmlspecialchars($row['materia_nombre']) . "</h3>";
                            echo "<p>" . htmlspecialchars($row['nombre_clase']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No tiene cursos asignados actualmente.</p>";
                    }
                    ?>
                </div>
            </div>

       <!-- Sección de Actividades -->
<div id="activities" class="section">
    <h2>Actividades del Curso</h2>
    <div id="activities-list">
        <!-- Las actividades se cargarán dinámicamente aquí -->
    </div>
    <button onclick="addActivity()">Agregar Actividad</button>
</div>


              <!-- Sección de Estudiantes-->
              <div id="students" class="section">
                <h2>Estudiantes Inscritos</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Lista de Estudiantes</h3>
                        <p>Visualiza los estudiantes inscritos en tus cursos.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Materiales -->
            <div id="materials" class="section">
                <h2>Gestión de Asignaturas</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Subir Material</h3>
                        <p>Agrega contenido para tus estudiantes.</p>
                    </div>
                    <div class="card">
                        <h3>Gestionar Archivos</h3>
                        <p>Actualiza o elimina material subido.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Configuración -->
            <div id="configuration" class="section">
                <h2>Configuración</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Actualizar Perfil</h3>
                        <p>Modifica tu información personal.</p>
                    </div>
                    <div class="card">
                        <h3>Cambiar Contraseña</h3>
                        <p>Establece una nueva contraseña.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>


        </main>
    </div>

    <script>
        // Función para mostrar las secciones
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }
    </script>
</body>
</html>



