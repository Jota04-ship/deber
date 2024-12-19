<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Estudiante</title>
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

        .stats-container, .course-container, .progress-container, .materials-container, .settings-container {
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
        <h2>Panel Estudiante</h2>
        <ul>
            <li><a href="#" onclick="showSection('home')">Inicio</a></li>
            <li><a href="#" onclick="showSection('courses')">Mis Cursos</a></li>
            <li><a href="#" onclick="showSection('progress')">Progreso</a></li>
            <li><a href="#" onclick="showSection('materials')">Materiales</a></li>
            <li><a href="#" onclick="showSection('settings')">Configuración</a></li>
            <li><a href="../../logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="content">
        <header>
            <h1>Bienvenido, [Nombre del Estudiante]</h1>
        </header>
        <main>
            <!-- Sección de Inicio -->
            <div id="home" class="section active">
                <h2>Estadísticas Generales</h2>
                <div class="stats-container">
                    <div class="card">
                        <h3>Cursos Inscritos</h3>
                        <p>4</p>
                    </div>
                    <div class="card">
                        <h3>Progreso General</h3>
                        <p>75%</p>
                    </div>
                    <div class="card">
                        <h3>Materiales Descargados</h3>
                        <p>20</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Cursos -->
            <div id="courses" class="section">
                <h2>Mis Cursos</h2>
                <div class="course-container">
                    <!-- Cursos dinámicos -->
                </div>
            </div>

            <!-- Sección de Progreso -->
            <div id="progress" class="section">
                <h2>Progreso</h2>
                <div class="progress-container">
                    <div class="card">
                        <h3>Curso: Matemáticas Avanzadas</h3>
                        <p>Progreso: 80%</p>
                    </div>
                    <div class="card">
                        <h3>Curso: Programación Básica</h3>
                        <p>Progreso: 50%</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Materiales -->
            <div id="materials" class="section">
                <h2>Materiales</h2>
                <div class="materials-container">
                    <div class="card">
                        <h3>Guía de Física</h3>
                        <p>PDF - Descargar</p>
                    </div>
                    <div class="card">
                        <h3>Presentación de Álgebra</h3>
                        <p>PPT - Descargar</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Configuración -->
            <div id="settings" class="section">
                <h2>Configuración</h2>
                <div class="settings-container">
                    <div class="card">
                        <h3>Cambiar Contraseña</h3>
                        <p>Haz clic para actualizar tu contraseña</p>
                    </div>
                    <div class="card">
                        <h3>Actualizar Perfil</h3>
                        <p>Modifica tu información personal</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Cursos de ejemplo
        const courses = [
            { name: "Matemáticas Avanzadas", description: "Curso de álgebra y cálculo avanzado." },
            { name: "Introducción a Física", description: "Conceptos básicos de mecánica y energía." },
            { name: "Programación Básica", description: "Aprende las bases de la programación." },
            { name: "Diseño Gráfico", description: "Fundamentos de diseño con herramientas digitales." }
        ];

        // Función para mostrar las secciones
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');

            if (sectionId === 'courses') {
                loadCourses();
            }
        }

        // Función para cargar cursos
        function loadCourses() {
            const courseContainer = document.querySelector('.course-container');
            courseContainer.innerHTML = ''; // Limpia el contenedor

            courses.forEach(course => {
                const courseElement = document.createElement('div');
                courseElement.classList.add('card');
                courseElement.innerHTML = `
                    <h3>${course.name}</h3>
                    <p>${course.description}</p>
                `;
                courseContainer.appendChild(courseElement);
            });
        }
    </script>
</body>
</html>


 
