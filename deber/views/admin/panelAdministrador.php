<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
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

        main {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 18px;
            color: #960019;
            margin-bottom: 10px;
        }

        .card:hover {
            transform: scale(1.05);
            transition: transform 0.3s;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Panel Administrador</h2>
        <ul>
            <li><a href="#" onclick="showSection('dashboard')">Inicio</a></li>
            <li><a href="#" onclick="showSection('manageUsers')">Gestión de Usuarios</a></li>
            <li><a href="#" onclick="showSection('manageCourses')">Gestión de Cursos</a></li>
            <li><a href="#" onclick="showSection('manageassignment')">Gestión de Asignaturas</a></li>
            <li><a href="#" onclick="showSection('reports')">Reportes</a></li>
            <li><a href="../../logout.php">Cerrar Sesión</a></li>
        </ul>
    </div> 
    <div class="content">
        <header>
            <h1>Bienvenido, Administrador</h1>
        </header>
        <main>
            <!-- Sección de Inicio -->
            <div id="dashboard" class="section active">
                <h2>Estadísticas Generales</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Total Usuarios</h3>
                        <p>150</p>
                    </div>
                    <div class="card">
                        <h3>Cursos Activos</h3>
                        <p>12</p>
                    </div>
                    <div class="card">
                        <h3>Reportes Generados</h3>
                        <p>8</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Gestión de Usuarios -->
            <div id="manageUsers" class="section">
                <h2>Gestión de Usuarios</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Crear Usuario</h3>
                        <p>Accede a la opción para agregar nuevos usuarios.</p>
                    </div>
                    <div class="card">
                        <h3>Editar Usuarios</h3>
                        <p>Actualiza la información de los usuarios existentes.</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Gestión de Cursos -->
            <div id="manageCourses" class="section">
                <h2>Gestión de Cursos</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Crear Curso</h3>
                        <p>Configura nuevos cursos para la plataforma.</p>
                    </div>
                    <div class="card">
                        <h3>Actualizar Cursos</h3>
                        <p>Modifica los cursos existentes y agrega contenido.</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Gestión de Asignaturas -->
            <div id="manageassignment" class="section">
                <h2>Gestión de Asignaturas</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Crear Asignatura</h3>
                        <p>Configura las nuevas asignaturas para la plataforma.</p>
                    </div>
                    <div class="card">
                        <h3>Actualizar Asignatura</h3>
                        <p>Modifica las asignaturas existentes y agrega contenido.</p>
                    </div>
                    <div class="card">
                        <h3>Eliminar Asignatura</h3>
                        <p>Elimina las asignaturas existentes.</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Reportes -->
            <div id="reports" class="section">
                <h2>Reportes</h2>
                <div class="card-container">
                    <div class="card">
                        <h3>Reporte de Usuarios</h3>
                        <p>Genera un informe sobre los usuarios registrados.</p>
                    </div>
                    <div class="card">
                        <h3>Reporte de Actividad</h3>
                        <p>Analiza el uso de la plataforma.</p>
                    </div>
                </div>
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

