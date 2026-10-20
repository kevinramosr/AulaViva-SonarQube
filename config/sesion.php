<?php

/* CONFIGURACIÓN SEGURA DE LA COOKIE DE SESIÓN */

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);

/* INICIAR SESIÓN */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* VERIFICAR AUTENTICACIÓN */

if (
    !isset(
        $_SESSION['usuario_id'],
        $_SESSION['colegio_id'],
        $_SESSION['rol']
    )
) {
    header('Location: /Aula%20viva/login.php');
    exit;
}