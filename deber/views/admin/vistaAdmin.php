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
            position: fixed;
            height: 100%;
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
            margin-left: 250px;
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
            cursor: pointer; /* Hace que la tarjeta parezca clicable */
            transition: transform 0.3s, box-shadow 0.3s;
            
        }

        .card h3 {
            font-size: 18px;
            color: #960019;
            margin-bottom: 10px;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .activities-list {
            margin-top: 20px;
        }

        .activity {
            background-color: #f4f4f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .activity button {
            margin-left: 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .activity button:hover {
            background-color: #c0392b;
        }

         /* Calendario */
         .calendar-box {
            border: 2px solid #960019; /* Borde rojo */
            border-radius: 10px;       /* Bordes redondeados */
            padding: 20px;             /* Espaciado interno */
            background-color: white;   /* Fondo blanco */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15); /* Sombra */
            max-width: 600px;          /* Ancho máximo del recuadro */
            margin: 20px auto;         /* Centrar el recuadro horizontalmente */
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr); /* 7 columnas para los días de la semana */
            gap: 5px;
            margin-top: 10px;
        }

        .calendar div {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
            background-color: #f4f4f9;
        }

        .calendar .header {
            font-weight: bold;
            color: #960019;
        }

        .calendar .today {
            background-color: #960019;
            color: white;
            font-weight: bold;
            border-radius: 50%;
        }

        /* Estilo del modal */
        .modal {
            background-color: white;
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .modal h2 {
            margin: 0;
            font-size: 22px;
            color: #ffffff;
            background-color: #b22223;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .modal p {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
            text-align: center;
        }

        .modal form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input[type="password"] {
            font-family: Arial, sans-serif;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .buttons button {
            padding: 10px 15px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: #ccc;
            color: #333;
        }

        .btn-confirm {
            background-color: #b22223;
            color: white;
        }

        .btn-cancel:hover, 
        .btn-confirm:hover {
            opacity: 0.9;
        }

        h4 {
            font-family: Arial, sans-serif;
            font-size: 24px;
            margin-top: 20px;  /* Añadir un espacio de 30px hacia abajo desde el contenedor de las tarjetas */
        }

        /* Estilo del botón de retroceso */
        .back-button {
            position: absolute;
            top: 10px;
            right: 60px;
            background-color: #fff6f6; /* Color verde educativo */
            border: none; /* Sin bordes */
            border-radius: 8px; /* Bordes redondeados */
            padding: 10px 15px; /* Espaciado interno */
            color: black; /* Texto blanco */
            font-size: 16px; /* Tamaño del texto */
            font-weight: bold; /* Texto en negrita */
            cursor: pointer; /* Manito al pasar el mouse */
            box-shadow: 0 4px 6px #efe5e5; /* Sombra para profundidad */
            transition: background-color 0.3s ease; /* Efecto suave al pasar el mouse */
        }

        /* Ícono dentro del botón */
        .back-icon {
            margin-right: 5px; /* Espaciado entre el ícono y el texto */
            font-size: 20px; /* Tamaño del ícono */
            color: #000;
        }

        /* Efecto hover */
        .back-button:hover {
            background-color: #ded4d4; /* Color más oscuro al pasar el mouse */
        }

        /* Adaptar el botón a pantallas pequeñas */
        @media (max-width: 600px) {
            .back-button {
                font-size: 14px; /* Reducir tamaño del texto */
                padding: 8px 12px; /* Reducir espaciado */
            }
            .back-icon {
                font-size: 18px; /* Ajustar tamaño del ícono */
            }
        }

        /* Estilo del botón de la casita */
        .home-button {
            position: absolute;
            top: 10px; /* Posición en la parte superior */
            right: 10px; /* En la esquina izquierda */
            background-color: #fff6f6; /* Color  (educativo y llamativo) */
            border: none; /* Sin bordes */
            border-radius: 8px; /* Bordes redondeados */
            padding: 10px 15px; /* Espaciado interno */
            color: black; /* Texto blanco */
            font-size: 16px; /* Tamaño del texto */
            font-weight: bold; /* Texto en negrita */
            cursor: pointer; /* Cambiar el cursor al pasar */
            box-shadow: 0 4px 6px #efe5e5; /* Sombra */
            transition: background-color 0.3s ease; /* Transición suave */
            display: flex; /* Para alinear ícono y texto */
            align-items: center; /* Centrar verticalmente */
            gap: 5px; /* Espaciado entre ícono y texto */
        }

        /* Ícono dentro del botón */
        .home-icon {
            font-size: 20px; /* Tamaño del ícono */
            color: #000;
        }

        /* Efecto hover */
        .home-button:hover {
            background-color: #ded4d4; /* Naranja más oscuro */
        }

        /* Adaptar el botón a pantallas pequeñas */
        @media (max-width: 600px) {
            .home-button {
                font-size: 14px; /* Reducir tamaño del texto */
                padding: 8px 12px; /* Reducir espaciado */
            }
            .home-icon {
                font-size: 18px; /* Ajustar tamaño del ícono */
            }
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
            <li><a href="#" onclick="showSection('configuration')">Configuración</a></li>
            <li><a href="log.php">Cerrar Sesión</a></li>
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
                    <div class="card" onclick="showSection('manageUsers')">
                        <h3>Total Usuarios</h3>
                        <p>150</p>
                    </div>
                    <div class="card" onclick="showSection('manageCourses')">
                        <h3>Cursos Activos</h3>
                        <p>12</p>
                    </div>
                </div>

                <h4>Fecha:</h4>
                <p id="current-date"></p>
                <div class="calendar-box">
                    <div class="calendar-container">
                        <div class="calendar" id="calendar-grid"></div>
                    </div>
                </div> 
            </div>

            <!-- Sección de Gestión de Usuarios -->
            <div id="manageUsers" class="section">
                <h2>Gestión de Usuarios</h2>
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <div class="card-container">
                    <div class="card" onclick="showSection('listUsers')">
                        <h3>Lista de Usuarios</h3>
                        <p>Accede a la opción para visualizar la lista de usuarios</p>
                    </div>
                    <div class="card" onclick="showSection('modal')">
                        <h3>Crear Usuario</h3>
                        <p>Accede a la opción para agregar nuevos usuarios.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Lista de Usuarios -->
            <div id="listUsers" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
            <h2>Gestión de Usuarios</h2>
                <div class="card-container">
                    <div class="card" onclick="showSection('listUsersDoc')">
                        <h3>Docentes</h3>
                        <p>Listado.</p>
                    </div>
                    <div class="card" onclick="showSection('listUsersStu')">
                        <h3>Estudiantes </h3>
                        <p>Listado.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Lista de Docentes -->
            <div id="listUsersDoc" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
            <h2>Listado Docentes en la Plataforma</h2>
                
            </div>

            <!-- Sección Lista de Estudiantes -->
            <div id="listUsersStu" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
            <h2>Listado Estudiantes en la Plataforma</h2>
                
            </div>

            <!-- Sección Creación de Usuarios -->
            <div id = 'modal' class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
                <h2>Crear nuevo usuario</h2>
                <p>Configura los usuarios de tu cuenta y asígnales un rol.</p>
                <form action="procesar.php" method="POST">

                    <!-- Identificación -->
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Apellido:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rol:</label>
                        <select id="role" name="role">
                            <option value="Administrador">Administrador</option>
                            <option value="Docente">Docente</option>
                            <option value="Estudiante">Estudiante</option>
                        </select>
                    </div>
                    

                    <!-- Cuenta -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmación de contraseña:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <!-- Botones -->
                    <div class="buttons">
                        <button type="button" class="btn-cancel" onclick="showSection('manageUsers')">Cancelar</button>
                        <button type="submit" class="btn-confirm">Confirmar</button>
                    </div>
            </form>
        </div>

            <!-- Sección de Gestión de Cursos -->
            <div id="manageCourses" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <h2>Gestión de Cursos</h2>
                <div class="card-container">
                    <div class="card" onclick="showSection('listCourses')">
                        <h3>Listado Cursos</h3>
                        <p>Visualiza los cursos existentes.</p>
                    </div>
                    <div class="card" onclick="showSection('modal1')">
                        <h3>Crear Curso </h3>
                        <p>Configura nuevos cursos para la plataforma.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Lista de Cursos -->
            <div id="listCourses" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
                <h2>Cursos</h2>
                
            </div>

            <!-- Sección Creación de Cursos -->
            <div id = 'modal1' class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
                <h2>Crear nuevo curso</h2>
                <p>Configura los cursos de las carreras.</p>
                <form action="procesar.php" method="POST">

                    <!-- Identificación -->
                    <div class="form-group">
                    <label for="carrera">Carrera:</label>
                        <select id="carrera" name="carrera">
                            <option value="Software">Software</option>
                            <option value="Tecnologías de la Información">Tecnologías de la Información</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Telecomunicaciones">Telecomunicaciones</option>
                            <option value="Automatización y Robótica">Automatización y Robótica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semestre">Semestre:</label>
                        <select id="semestre" name="semestre">
                            <option value="Nivelacion">Nivelación</option>
                            <option value="Primero">Primero</option>
                            <option value="Segundo">Segundo</option>
                            <option value="Tercero">Tercero</option>
                            <option value="Cuarto">Cuarto</option>
                            <option value="Quinto">Quinto</option>
                            <option value="Sexto">Sexto</option>
                            <option value="Septimo">Séptimo</option>
                            <option value="Octavo">Octavo</option>
                            <option value="Noveno">Noveno</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paralelo">Paralelo:</label>
                        <select id="paralelo" name="paralelo">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <!-- Botones -->
                    <div class="buttons">
                        <button type="button" class="btn-cancel" onclick="showSection('manageCourses')">Cancelar</button>
                        <button type="submit" class="btn-confirm">Confirmar</button>
                    </div>
            </form>
        </div>

            <!-- Sección de Gestión de Asignaturas -->
            <div id="manageassignment" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <h2>Gestión de Asignaturas</h2>
                <div class="card-container">
                    <div class="card" onclick="showSection('listAsignaturas')">
                        <h3>Actualizar Asignatura</h3>
                        <p>Modifica las asignaturas existentes y agrega contenido.</p>
                    </div>
                    <div class="card" onclick="showSection('modal2')">
                        <h3>Crear Asignatura</h3>
                        <p>Configura las nuevas asignaturas para la plataforma.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Actualizar Asignaturas-->
            <div id="listAsignaturas" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
                <h2>Asignaturas</h2>
                
            </div>

            <!-- Sección Creación de Asignaturas -->
            <div id = 'modal2' class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
                <h2>Crear nuevo curso</h2>
                <p>Configura los cursos de las carreras.</p>
                <form action="procesar.php" method="POST">

                    <!-- Identificación -->
                    <div class="form-group">
                    <label for="carrera">Carrera:</label>
                        <select id="carrera" name="carrera">
                            <option value="Software">Software</option>
                            <option value="Tecnologías de la Información">Tecnologías de la Información</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Telecomunicaciones">Telecomunicaciones</option>
                            <option value="Automatización y Robótica">Automatización y Robótica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semestre">Semestre:</label>
                        <select id="semestre" name="semestre">
                            <option value="Nivelacion">Nivelación</option>
                            <option value="Primero">Primero</option>
                            <option value="Segundo">Segundo</option>
                            <option value="Tercero">Tercero</option>
                            <option value="Cuarto">Cuarto</option>
                            <option value="Quinto">Quinto</option>
                            <option value="Sexto">Sexto</option>
                            <option value="Septimo">Séptimo</option>
                            <option value="Octavo">Octavo</option>
                            <option value="Noveno">Noveno</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paralelo">Paralelo:</label>
                        <select id="paralelo" name="paralelo">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="asignatura">Asignatura:</label>
                        <input type="asignatura" id="asignatura" name="asignatura" required>
                    </div>
                    <!-- Botones -->
                    <div class="buttons">
                        <button type="button" class="btn-cancel" onclick="showSection('manageassignment')">Cancelar</button>
                        <button type="submit" class="btn-confirm">Confirmar</button>
                    </div>
            </form>
        </div>

            <!-- Sección Configuración -->
            <div id="configuration" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <h2>Configuración</h2>
                <div class="card-container">
                    <div class="card" onclick="showSection('perfil')">
                        <h3>Actualizar Perfil</h3>
                        <p>Modifica tu información personal.</p>
                    </div>
                    <div class="card" onclick="showSection('password1')">
                        <h3>Cambiar Contraseña</h3>
                        <p>Establece una nueva contraseña.</p>
                    </div>
                </div>
            </div>

            <!-- Sección Actualización de Perfil -->
            <div id="perfil" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
            <h2>Actualizar Perfil</h2>
                <div class="form-group">
                    <form action="procesar.php" method="POST">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Juan Pérez" required>

                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="juan.perez@example.com" readonly>

                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="0987654321">

                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" placeholder="Calle 123, Barrio, Ciudad">

                    <!-- Botones -->
                    <div class="buttons">
                        <button type="button" class="btn-cancel" onclick="showSection('configuration')">Cancelar</button>
                        <button type="submit" class="btn-confirm">Guardar Cambios</button>
                    </div>
                    </form>
                </div>
            </div>

            <!-- Sección Cambiar Contraseña -->
            <div id="password1" class="section">
                <button onclick="goToHome()" class="home-button">
                    <span class="home-icon">⌂</span>
                </button>
                <button onclick="goBack()" class="back-button">  <!-- Botón de retroceso -->
                    <span class="back-icon">←</span> Volver
                </button>
            <h2>Cambiar Contraseña</h2>
                <div class="form-group">
                    <form action="procesar.php" method="POST">
                        <label for="actual">Contraseña Actual</label>
                        <input type="password" id="actual" name="actual" placeholder="Ingresa tu contraseña actual" required>

                        <label for="nueva">Nueva Contraseña</label>
                        <input type="password" id="nueva" name="nueva" placeholder="Ingresa tu nueva contraseña" required>

                        <label for="confirmar">Confirmar Contraseña</label>
                        <input type="password" id="confirmar" name="confirmar" placeholder="Confirma tu nueva contraseña" required>

                        <!-- Botones -->
                        <div class="buttons">
                            <button type="button" class="btn-cancel" onclick="showSection('configuration')">Cancelar</button>
                            <button type="submit" class="btn-confirm">Guardar Cambios</button>
                        </div>
                    
                    </form>
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

        // Mostrar fecha en la tarjeta de calendario
        function addDateToCalendarCard() {
            const currentDate = new Date();
            const formattedDate = currentDate.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const dateElement = document.getElementById('current-date-card');
            if (dateElement) {
                dateElement.textContent = formattedDate;
            }
        }

        // Generar calendario
        function generateCalendar() {
            const calendarGrid = document.getElementById('calendar-grid');
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = currentDate.getDate();

            // Mostrar la fecha actual debajo del título
            document.getElementById('current-date').textContent = `Hoy es ${currentDate.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}`;

            // Crear encabezado de días
            const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            daysOfWeek.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.textContent = day;
                dayHeader.className = 'header';
                calendarGrid.appendChild(dayHeader);
            });

            // Crear días del calendario
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const offset = (firstDay + 6) % 7;

            for (let i = 0; i < offset; i++) {
                const emptyCell = document.createElement('div');
                calendarGrid.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;
                if (day === today) {
                    dayCell.className = 'today';
                }
                calendarGrid.appendChild(dayCell);
            }
        }

        // Ejecutar funciones al cargar la página
        window.onload = () => {
            addDateToCalendarCard();
            generateCalendar();
        };

        // Array para almacenar el historial de secciones
        let sectionHistory = [];

        // Función para mostrar una sección específica
        function showSection(sectionId) {
            // Obtener la sección actualmente visible
            const currentSection = document.querySelector('.section.active');
            if (currentSection) {
                // Guardar el ID de la sección actual en el historial
                sectionHistory.push(currentSection.id);
                currentSection.classList.remove('active');
            }
            // Mostrar la nueva sección
            document.getElementById(sectionId).classList.add('active');
        }

        // Función para retroceder a la sección anterior
        function goBack() {
            if (sectionHistory.length > 0) {
                // Sacar el último ID del historial
                const lastSectionId = sectionHistory.pop();
                const currentSection = document.querySelector('.section.active');
                if (currentSection) {
                    currentSection.classList.remove('active');
                }
                // Mostrar la sección anterior
                document.getElementById(lastSectionId).classList.add('active');
            } else {
                alert("No hay historial para regresar.");
            }
        }

        // Función para ir a la pantalla de inicio
        function goToHome() {
            showSection('dashboard'); // Cambia 'home' por la sección correspondiente
        }


    </script>
</body>
</html>

