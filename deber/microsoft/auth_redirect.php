<?php
session_start();
error_reporting(E_ALL);  // Mostrar todos los errores
ini_set('display_errors', 1);  // Asegurarse de que los errores se muestren

// Verifica si el parámetro 'code' está presente en la URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];  // El código de autorización recibido de Azure AD
    $clientId = '5c9ac2b8-b927-4ff0-8085-b930c9f0c331';  // Reemplaza con tu Client ID
    $clientSecret = 'T3j8Q~QdEBNQb5BxQgPijI9.W3R2e~ifQV9gYdcx';  // Reemplaza con tu Client Secret
    $tenantId = 'a988ccd4-00ed-4bf3-a4d1-b5661f44abdf';  // Reemplaza con tu Tenant ID
    $redirectUri = 'http://localhost/deber/microsoft/auth_redirect.php'; // Debe coincidir con el registrado en Azure

    // Solicitar el token de acceso
    $url = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";
    $data = [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'code' => $code,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code',
        'scope' => 'openid profile User.Read'
    ];

    // Configuración de la solicitud POST
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        echo "Error al obtener el token de acceso.";
        exit();
    }

    // Decodifica la respuesta
    $responseData = json_decode($response, true);

    // Verifica si el token de acceso está presente
    if (isset($responseData['access_token'])) {
        $_SESSION['access_token'] = $responseData['access_token'];
        
        
        // Obtener la información del usuario desde Microsoft Graph
        $userInfo = getUserInfo($responseData['access_token']);
        var_dump($userInfo);  // Esto te mostrará toda la respuesta de Graph
exit();

        // Debugging: Verificar la respuesta de la API de Microsoft Graph
        // var_dump($userInfo); // Descomenta esto para ver la respuesta completa y depurar
        // exit();

        // Verifica si se obtuvieron roles
        if (isset($userInfo['value'])) {
            $roles = extractRoles($userInfo['value']);  // Extraemos los roles desde los grupos
            $role = determineUserRole($roles);          // Determinamos el rol

            // Debugging: Verificar el rol asignado
            // echo "Rol asignado: $role"; // Descomenta esto para ver qué rol se asigna
            // exit();

            // Redirigir según el rol del usuario
            redirectUserBasedOnRole($role);
        } else {
            echo "No se encontraron roles para el usuario.";
            exit();
        }
    } else {
        echo "Error al obtener el token de acceso.";
    }
} else {
    echo "Código de autorización no encontrado.";
}

// Función para obtener la información del usuario desde Microsoft Graph
// Función para obtener la información del usuario desde Microsoft Graph
// Función para obtener la información del usuario desde Microsoft Graph, manejando la paginación
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

    // Comprobar si hubo un error en la solicitud
    if ($response === FALSE) {
        echo "Error al obtener los grupos.";
        exit();
    }

    // Decodificar la respuesta
    $responseData = json_decode($response, true);

    // Comprobar si hay un error en la respuesta
    if (isset($responseData['error'])) {
        echo "Error: " . $responseData['error']['message'];
        exit();
    }

    // Manejo de paginación: si hay más grupos, se obtiene el siguiente conjunto de grupos
    while (isset($responseData['@odata.nextLink'])) {
        $nextUrl = $responseData['@odata.nextLink'];
        $response = file_get_contents($nextUrl, false, $context);
        $responseData['value'] = array_merge($responseData['value'], json_decode($response, true)['value']);
    }

    // Devolver la respuesta con todos los grupos
    return $responseData['value'];
}

// Función para obtener detalles de un grupo específico (si es necesario)
function getGroupDetails($groupId, $accessToken) {
    $url = "https://graph.microsoft.com/v1.0/groups/$groupId";  // Obtener detalles del grupo
    $headers = ["Authorization: Bearer $accessToken"];
    
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    return json_decode($response, true);
}

// Función para extraer los roles desde los grupos de Microsoft Graph
function extractRoles($groups, $accessToken) {
    $roles = [];
    
    foreach ($groups as $group) {
        // Si el displayName está vacío o no disponible, obtener detalles del grupo
        if (empty($group['displayName'])) {
            $groupDetails = getGroupDetails($group['id'], $accessToken);
            $group['displayName'] = $groupDetails['displayName'] ?? ''; // Asignar displayName si se encuentra
        }

        // Asegúrate de que el nombre del grupo coincida con los roles disponibles
        if (in_array($group['displayName'], ['Administrador', 'Profesor', 'Estudiante'], true)) {
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
            header("Location: http://localhost/deber/admin/admin_dashboard.php");
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
