<?php

/* INICIAR LA SESIÓN */

session_start();

/* ELIMINAR LOS DATOS DE LA SESIÓN */

$_SESSION = [];

/* ELIMINAR LA COOKIE DE SESIÓN */

if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]
    );
}

/* DESTRUIR LA SESIÓN */

session_destroy();

/* VOLVER AL LOGIN */

header('Location: login.php');
exit;   