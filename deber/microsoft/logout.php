<?php
session_start();

// Destruir la sesión actual en tu aplicación
session_unset();   // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir al usuario a Azure AD para cerrar sesión en su cuenta Microsoft
$tenantId = 'a988ccd4-00ed-4bf3-a4d1-b5661f44abdf';  // Tu tenant ID
$redirectUri = 'http://localhost/deber/microsoft/logout.php';  // La URI de redirección luego del logout

// Redirigir al usuario a la página de logout de Azure AD
header("Location: https://login.microsoftonline.com/$tenantId/oauth2/v2.0/logout?post_logout_redirect_uri=$redirectUri");
exit();
?>
