<?php
declare(strict_types=1);

$_SERVER['REQUEST_URI'] = '/api/v1/aulas/';

require_once __DIR__ . '/../../api/v1/_helpers/api.php';

$caso = $argv[1] ?? '';

if ($caso === 'sin-sesion' || $caso === 'rol-incorrecto') {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION = [];

    if ($caso === 'sin-sesion') {
        exigirRolApi('profesor');
    }

    $_SESSION['usuario_id'] = 1;
    $_SESSION['rol'] = 'alumno';

    exigirRolApi('profesor');
}

if ($caso === 'responder-problema') {
    responderProblema(
        422,
        'Datos inválidos',
        'Falta un campo obligatorio.',
        '/errors/datos-invalidos'
    );
}

fwrite(STDERR, 'Caso de prueba desconocido.');
exit(1);
