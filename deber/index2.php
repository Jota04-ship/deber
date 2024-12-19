<?php

$imagen = 'img.png';
$imagenEnca = 'encabezado.png';
$imagenPlataforma = 'plataforma.png';
$imagenMicrosoft = 'microsoft.png';
$imagenfondo = 'fondo.png';


session_start(); // Iniciar la sesión
// Incluir la conexión a la base de datos
include 'includes/db.php';  // Asegúrate de que la ruta es correcta
// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // Obtener el tipo de login (puede ser 'normal' o 'microsoft')
    $login_type = isset($_POST['login_type']) ? $_POST['login_type'] : 'normal';

   // Si el tipo de login es 'normal' (correo y contraseña)
   if ($login_type === 'normal') {


    // Obtener correo y contraseña desde el formulario
    $correo = htmlspecialchars($_POST['correo']);  // Usar 'correo' en lugar de 'username'
    $contraseña = htmlspecialchars($_POST['contraseña']);  // Usar 'contraseña' en lugar de 'password'

  
    // Consulta para obtener los datos del usuario desde la base de datos
    $query = "SELECT * FROM usuarios WHERE correo = '$correo'";  // Cambiar 'username' por 'correo'
    $result = mysqli_query($conn, $query); // $conn es la conexión a la base de datos

    if ($result && mysqli_num_rows($result) > 0) {
        // Si el usuario existe, obtener los datos
        $usuario = mysqli_fetch_assoc($result);

    // Verificar si la contraseña es correcta (usando password_verify si la contraseña está encriptada)
    if (password_verify($contraseña, $usuario['contraseña'])) {  // Usar 'contraseña' en lugar de 'password'
        // Almacenar la información del usuario en la sesión
        $_SESSION['usuario_id'] = $usuario['id'];  // Almacenar el ID del usuario
        $_SESSION['tipo'] = $usuario['tipo'];  // Almacenar el tipo de usuario (administrador, profesor, estudiante)

       // Redirigir según el tipo de usuario
       if ($usuario['tipo'] === 'administrador') {
        header("Location: views/admin/vistaAdmin.php");
    } elseif ($usuario['tipo'] === 'profesor') {
        header("Location: views/profesor/panelDocente.php");
    } elseif ($usuario['tipo'] === 'estudiante') {
        header("Location: views/estudiante/panelEstudiante.php");
    }
        exit();  // Asegurarse de detener la ejecución después de la redirección
    } else {
        $error = "Contraseña incorrecta.";  // Si la contraseña no coincide
    }
} else {
    $error = "Correo no registrado.";  // Si no existe el correo en la base de datos
}
 // Si el tipo de login es 'microsoft'
}elseif ($login_type === 'microsoft') {
    // Aquí puedes manejar el inicio de sesión con Microsoft (usualmente con OAuth u otra API)
    // Por ahora, solo puedes redirigir o manejar la lógica para Microsoft
    header("Location: microsoft/login.php");  // Redirigir al proceso de inicio de sesión de Microsoft
    exit();
}
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar con</title>
    <style> 
  
        body {
            font-family: Arial, sans-serif;
            background-image: url('<?php echo $imagenfondo; ?>');
            background-size: cover; /* Ajusta la imagen al tamaño de la ventana */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            background-position: center center; /* Centra la imagen */
            height: 100vh; /* Ocupa toda la altura de la ventana */
            margin: 0; /* Elimina el margen */
            display: flex; /* Alineación del contenido */
            justify-content: center; /* Centra horizontalmente */
            align-items: center; /* Centra verticalmente */
        }

        .container {
            display: flex;
            background-color: rgba(255, 255, 255, 0.4); /* Fondo blanco semi-transparente */
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 800px;
            width: 100%;
        }

        .left, .right {
            width: 50%;
            padding: 20px;
        }

        .left {
            text-align: center;
            border-right: 1px solid #ccc;
        }

        .right {
            padding-left: 40px;
        }

        h2 {
            text-align: center;
        }

        label, input {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.5); /* Fondo semi-transparente para los campos de entrada */
        }

        button {
            background-color: white;
            border: 1px solid #B8860B;
            border-radius: 5px;
            padding: 10px;
            width: 50%;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .container {
            text-align: center; /* Centrará el contenido del contenedor */
            margin-top: 50px; /* Espaciado superior */
        }

        button:hover {
            background-color: #f0f0f0;
        }

        .error {
            color: red;
            text-align: center;
        }

        .select-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .select-container select {
            margin-right: 10px; /* Añade un margen derecho al menú de opciones */
        }

        .AvisoCookies {
            background-color: white;
            color: #92000A; /* Cambia el color de la frase Aviso de Cookies */
            font-size: 15px; /* Cambia el tamaño de la frase Aviso de Cookies */
            font-weight: bold;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            margin-left: 10px; /* Espaciado entre el botón y el menú */
        }

        .AvisoCookies:hover {
            background-color: #f0f0f0;
        }

        .button-icon {
            margin-right: 8px;
        }

        /* Imagen en la parte inferior izquierda de la página web */
        .bottom-image {
            position: fixed; /* Fijo a la página */
            bottom: 10px; /* A 10px del borde inferior */
            left: 10px; /* A 10px del borde izquierdo */
            z-index: 100; /* Para que se superponga a otros elementos */
        }

        .modal {
            display: none; /* Oculto por defecto */
            position: fixed;
            z-index: 1000; /* Asegura que esté encima de otros elementos */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro transparente */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            text-align: left;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-header {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .modal-body {
            font-size: 14px;
            line-height: 1.5;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #92000A;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
        }

        .close-btn:hover {
            background-color: #c00;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="left">
           <h1>Hello programers</h1>
           <h1>Esta es la segunda prueba</h1>
            <img src="<?php echo $imagenEnca; ?>" width="277" height="75">
            <img src="<?php echo $imagenPlataforma; ?>" width="277" height="74">
            <p id="faculty-text">Facultad de Ingeniería en Sistemas, Electrónica e Industrial</p>
        </div>

        <div class="right">
            <h2 id="login-with">Ingresar con</h2>
            
            <form method="POST" action="">
    <label for="correo" id="label-username">Correo:</label>
    <input type="email" name="correo" placeholder="Correo" required>

    <label for="contraseña" id="label-password">Contraseña:</label>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    
    <!-- Campo oculto para el tipo de login -->
    <input type="hidden" name="login_type" value="normal" id="login-type">  <!-- Valor predeterminado -->

    <div class="button-container">
        <!-- Botón de "Ingresar" -->
        <button type="submit" class="enter-btn">
            <span id="enter-login">Ingresar</span>
        </button>

        <!-- Botón de "Microsoft Office 365" -->
        <button type="button" class="microsoft-btn" onclick="document.getElementById('login-type').value = 'microsoft'; this.form.submit();">
            <img src="<?php echo $imagenMicrosoft; ?>" width="20" height="20" class="button-icon">
            <span id="microsoft_login">Microsoft Office 365</span>
        </button>
    </div>
</form>


            <?php if (isset($error)) { echo "<p class='error' id='error-msg'>$error</p>"; } ?>

            <div class="select-container">
                <select id="language-select">
                    <option value="es">Español-Internacional(es)</option>
                    <option value="en">English(en)</option>
                </select>
                <button class="AvisoCookies" id="cookie-notice">Aviso de Cookies</button>
            </div>
        </div>
    </div>

    <script>
        // Objeto con las traducciones
        const translations = {
    en: {
        faculty: "Faculty of Systems, Electronics, and Industrial Engineering",
        loginWith: "Sign in with",
        username: "Username:",
        password: "Password:",
        microsoft: "Microsoft Office 365",
        cookieNotice: "Cookie Notice",
        error: "Incorrect username or password.",
        modalTitle: "'Cookies' must be enabled in your browser",
        modalBody: `
            This website uses two cookies:
            <ul>
                <li><strong>The essential cookie</strong> is the session cookie, usually called <em>MoodleSession</em>. You must allow this cookie in your browser to provide continuity and remain logged in while browsing the site. When you log out or close the browser, this cookie is deleted (both in the browser and on the server).</li>
                <li><strong>The other cookie</strong> is purely for convenience, usually called <em>MOODLEID</em> or similar. This simply remembers your username in the browser. This means that when you return to this site, the username field on the login page will already be filled in. It is safe to refuse this cookie – you will just have to retype your username each time you log in.</li>
            </ul>
        `
    },
    es: {
        faculty: "Facultad de Ingeniería en Sistemas, Electrónica e Industrial",
        loginWith: "Ingresar con",
        username: "Usuario:",
        password: "Contraseña:",
        microsoft: "Microsoft Office 365",
        cookieNotice: "Aviso de Cookies",
        error: "Usuario o contraseña incorrectos.",
        modalTitle: "Las 'Cookies' deben estar habilitadas en su navegador",
        modalBody: `
            Este sitio web utiliza dos cookies:
            <ul>
                <li><strong>La cookie esencial</strong> es la cookie de sesión, normalmente llamada <em>MoodleSession</em>. Debe permitir esta cookie en su navegador para dar continuidad y permanecer conectado mientras navega por el sitio. Cuando cierre la sesión o cierre el navegador, esta cookie se borra (en el navegador y en el servidor).</li>
                <li><strong>La otra cookie</strong> es puramente por conveniencia, normalmente llamada <em>MOODLEID</em> o similar. Esta solo recuerda su nombre de usuario en el navegador. Esto significa que cuando regrese a este sitio, el campo de nombre de usuario en la página de inicio de sesión ya estará completado. Es seguro rechazar esta cookie - solo tendrá que volver a escribir su nombre de usuario cada vez que inicie sesión.</li>
            </ul>
        `
    }
};

        // Función para cambiar el idioma
        function changeLanguage(lang) {
    document.getElementById("faculty-text").textContent = translations[lang].faculty;
    document.getElementById("login-with").textContent = translations[lang].loginWith;
    document.getElementById("label-username").textContent = translations[lang].username;
    document.getElementById("label-password").textContent = translations[lang].password;
    document.getElementById("microsoft-login").textContent = translations[lang].microsoft;
    document.getElementById("cookie-notice").textContent = translations[lang].cookieNotice;

    // Cambiar el contenido de la ventana emergente
    document.querySelector(".modal-header").textContent = translations[lang].modalTitle;
    document.querySelector(".modal-body").innerHTML = translations[lang].modalBody;

    const errorMsg = document.getElementById("error-msg");
    if (errorMsg) {
        errorMsg.textContent = translations[lang].error;
    }
}


        // Evento para cambiar el idioma
        document.getElementById("language-select").addEventListener("change", function () {
            const selectedLanguage = this.value;
            changeLanguage(selectedLanguage);
        });
    </script>

    <!-- Ventana emergente -->
    <div class="modal" id="cookie-modal">
        <div class="modal-content">
            <button class="close-btn" id="close-modal">&times;</button>
            <div class="modal-header">Las 'Cookies' deben estar habilitadas en su navegador</div>
            <div class="modal-body">
                Este sitio web utiliza dos cookies:
                <ul>
                    <li>
                        <strong>La cookie esencial</strong> es la cookie de sesión, normalmente llamada <em>MoodleSession</em>. Debe permitir esta cookie en su navegador para dar continuidad y permanecer conectado mientras navega por el sitio. Cuando cierre la sesión o cierre el navegador, esta cookie se borra (en el navegador y en el servidor).
                    </li>
                    <li>
                        <strong>La otra cookie</strong> es puramente por conveniencia, normalmente llamada <em>MOODLEID</em> o similar. Esta solo recuerda su nombre de usuario en el navegador. Esto significa que cuando regrese a este sitio, el campo de nombre de usuario en la página de inicio de sesión ya estará completado. Es seguro rechazar esta cookie - solo tendrá que volver a escribir su nombre de usuario cada vez que inicie sesión.
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Mostrar la ventana emergente
        document.getElementById("cookie-notice").addEventListener("click", function () {
            document.getElementById("cookie-modal").style.display = "flex";
        });

        // Cerrar la ventana emergente
        document.getElementById("close-modal").addEventListener("click", function () {
            document.getElementById("cookie-modal").style.display = "none";
        });

        // Cerrar la ventana emergente al hacer clic fuera de ella
        window.addEventListener("click", function (e) {
            const modal = document.getElementById("cookie-modal");
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>

</body>
</html>
