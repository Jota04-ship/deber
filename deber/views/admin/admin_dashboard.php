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

        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #960019;
        }

        /* Estilo para los enlaces de gestión */
        .management-section {
            margin-bottom: 30px;
        }

        .management-section h2 {
            font-size: 20px;
            color: #960019;
            margin-bottom: 15px;
        }

        .management-section a {
            display: block;
            margin-bottom: 10px;
            color: #960019;
            text-decoration: none;
            transition: color 0.3s;
        }

        .management-section a:hover {
            color: #b22223;
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
            <h1>Bienvenido, Administrador</h1>
        </header>
        <main>
            <!-- Sección de estadísticas o resumen -->
            <div class="cards">
                <div class="card">
                    <h3>Total de Usuarios</h3>
                    <p>150</p>
                </div>
                <div class="card">
                    <h3>Cursos Disponibles</h3>
                    <p>12</p>
                </div>
                <div class="card">
                    <h3>Reportes Generados</h3>
                    <p>8</p>
                </div>
            </div>

            <!-- Secciones de gestión -->
            <div class="management-section">
                <h2>Gestión de Materias</h2>
                <a href="insert_materias.php">Crear Nueva Materia</a>
                <a href="ver_materias.php">Ver Materias</a>
            </div>

            <div class="management-section">
                <h2>Gestión de Clases</h2>
                <a href="insert_clases.php">Crear Nueva Clase</a>
                <a href="ver_clases.php">Ver Clases</a>
            </div>

            <div class="management-section">
                <h2>Gestión de Estudiantes</h2>
                <a href="ver_estudiantes.php">Ver Estudiantes</a>
                <a href="insert_user.php">Crear Nuevo Usuario</a>
            </div>

            <a href="../../logout.php">Cerrar sesión</a>
        </main>
    </div>
</body>
</html>
