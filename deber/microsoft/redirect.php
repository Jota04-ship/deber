<?php
session_start();

// Verifica si el parámetro 'code' está presente en la URL
if (isset($_GET['code'])) {
    // Si 'code' está presente, lo obtenemos
    $code = $_GET['code'];
    $clientId = '5c9ac2b8-b927-4ff0-8085-b930c9f0c331';  // Reemplaza con tu Client ID
    $clientSecret = 'T3j8Q~QdEBNQb5BxQgPijI9.W3R2e~ifQV9gYdcx';  // Reemplaza con tu Client Secret
    $tenantId = 'a988ccd4-00ed-4bf3-a4d1-b5661f44abdf';  // Reemplaza con tu Tenant ID
    $redirectUri = 'http://localhost/deber/microsoft/redirect.php'; // Debe coincidir con el registrado en Azure

    // Preparamos los datos para enviar en la solicitud POST
    $data = [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'code' => $code,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code',
        'scope' => 'openid profile User.Read'  // Verifica que estos permisos estén configurados en Azure
    ];

    // Usamos cURL en lugar de file_get_contents para mayor control
    $ch = curl_init("https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token");

    // Configuramos cURL para la solicitud POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));  // Codificamos los datos en formato URL
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

    // Ejecutamos la solicitud
    $response = curl_exec($ch);

    // Verificamos si hubo errores en la solicitud cURL
    if (curl_errno($ch)) {
        echo "Error en cURL: " . curl_error($ch);
        exit();
    }

    // Cerramos la conexión cURL
    curl_close($ch);

    // Mostramos la respuesta para depuración
    var_dump($response);  // Esto es importante para ver qué retorna la API de Microsoft

    // Decodificamos la respuesta JSON
    $responseData = json_decode($response, true);

    // Verifica si el token de acceso está presente en la respuesta
    if (isset($responseData['access_token'])) {
        $_SESSION['access_token'] = $responseData['access_token'];
        
        // Obtener la información del usuario desde Microsoft Graph
        $userInfo = getUserInfo($responseData['access_token']);

        // Verifica si se obtuvieron roles
        if (isset($userInfo['value'])) {
            $roles = extractRoles($userInfo['value']);  // Extraemos los roles desde los grupos
            $role = determineUserRole($roles);          // Determinamos el rol

            // Redirigir según el rol del usuario
            redirectUserBasedOnRole($role);
        } else {
            echo "No se encontraron roles para el usuario.";
            exit();
        }
    } else {
        // Si no hay access_token, mostramos la respuesta completa para ayudar a depurar el error
        echo "Error al obtener el token de acceso: ";
        var_dump($responseData);  // Muestra la respuesta completa de Microsoft
        exit();
    }
} else {
    echo "Código de autorización no encontrado.";
    exit();
}

// Función para obtener la información del usuario desde Microsoft Graph
function getUserInfo($accessToken) {
    $url = "https://graph.microsoft.com/v1.0/me/memberOf";  // Obtiene los grupos del usuario
    $headers = ["Authorization: Bearer $accessToken"];
    
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    return json_decode($response, true);  // Decodifica la respuesta
}

// Función para extraer los roles desde los grupos de Microsoft Graph
function extractRoles($groups) {
    $roles = [];
    foreach ($groups as $group) {
        // Asumiendo que los roles son nombres de los grupos, filtra los relevantes
        if (in_array($group['displayName'], ['Administrador', 'Profesor', 'Estudiante'])) {
            $roles[] = $group['displayName'];
        }
    }
    return $roles;
}

// Función para determinar el rol del usuario
function determineUserRole($roles) {
    if (in_array('Administrador', $roles)) {
        return 'admin';
    } elseif (in_array('Profesor', $roles)) {
        return 'professor';
    } elseif (in_array('Estudiante', $roles)) {
        return 'student';
    } else {
        return 'guest';  // Si no pertenece a ningún grupo reconocido
    }
}

// Redirigir según el rol del usuario
function redirectUserBasedOnRole($role) {
    switch ($role) {
        case 'admin':
            header("Location: http://localhost/deber/admin/admis_dashboard.php");
            break;
        case 'professor':
            header("Location: http://localhost/deber/profesor/profesor_dashboard.php");
            break;
        case 'student':
            header("Location: http://localhost/deber/estudiante/estudiante_dashboard.php");
            break;
        default:
            header("Location: http://localhost/deber/guest_dashboard.php");
            break;
    }
    exit();
}
?>
