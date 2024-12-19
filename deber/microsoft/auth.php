<?php 
session_start();

// Verifica si el parámetro 'code' está presente en la URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];  // El código de autorización recibido de Azure AD
    $clientId = '5c9ac2b8-b927-4ff0-8085-b930c9f0c331';  // Reemplaza con tu Client ID
    $clientSecret = 'T3j8Q~QdEBNQb5BxQgPijI9.W3R2e~ifQV9gYdcx';  // Reemplaza con tu Client Secret
    $tenantId = 'a988ccd4-00ed-4bf3-a4d1-b5661f44abdf';  // Reemplaza con tu Tenant ID
    $redirectUri = 'http://localhost/deber/microsoft/redirect.php'; // Debe coincidir con el registrado en Azure

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

// Depuración de los parámetros antes de hacer la solicitud
var_dump($data); // Ver los datos que se están enviando

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
        echo "Error al obtener el token de acceso.";
    }
} else {
    echo "Código de autorización no encontrado.";
}

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

    if ($response === FALSE) {
        echo "Error al obtener los grupos.";
        exit();
    }

    $responseData = json_decode($response, true);

    if (isset($responseData['error'])) {
        echo "Error: " . $responseData['error']['message'];
        exit();
    }

    // Manejo de paginación
    while (isset($responseData['@odata.nextLink'])) {
        $nextUrl = $responseData['@odata.nextLink'];
        $response = file_get_contents($nextUrl, false, $context);
        $responseData['value'] = array_merge($responseData['value'], json_decode($response, true)['value']);
    }

    return $responseData['value'];
}

// Función para extraer los roles desde los grupos de Microsoft Graph
function extractRoles($groups) {
    $roles = [];
    
    foreach ($groups as $group) {
        // Si el displayName está vacío, se deja en blanco
        $groupName = !empty($group['displayName']) ? $group['displayName'] : 'Desconocido';

        // Asegúrate de que el nombre del grupo coincida con los roles disponibles
        if (in_array($groupName, ['Administrador', 'Profesor', 'Estudiante'], true)) {
            $roles[] = $groupName;
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
