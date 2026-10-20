<?php

declare(strict_types=1);

class RespuestaApiTerminada extends RuntimeException
{
}

/**
 * Construye la respuesta de error según RFC 7807.
 */
function construirProblema(
    int $status,
    string $title,
    string $detail,
    string $type = 'about:blank'
): array {
    return [
        'type' => $type,
        'title' => $title,
        'status' => $status,
        'detail' => $detail,
        'instance' => $_SERVER['REQUEST_URI'] ?? ''
    ];
}

/**
 * Envía la respuesta de error y termina la ejecución.
 */

function responderProblema(
    int $status,
    string $title,
    string $detail,
    string $type = 'about:blank'
): never {
    http_response_code($status);

    header('Content-Type: application/problem+json; charset=utf-8');

    echo json_encode(
        construirProblema($status, $title, $detail, $type),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if (defined('PHPUNIT_API_TEST') && PHPUNIT_API_TEST === true) {
    throw new RespuestaApiTerminada();
}

exit;
}

/**
 * Comprueba la autorización de un usuario en la API.
 *
 * null = acceso permitido
 * 401  = usuario no autenticado
 * 403  = usuario sin permisos
 */
function verificarPermisoApi(
    string $rolRequerido,
    array $sesion
): ?int {
    if (!isset($sesion['usuario_id'], $sesion['rol'])) {
        return 401;
    }

    if ($sesion['rol'] !== $rolRequerido) {
        return 403;
    }

    return null;
}
/*Comprueba que exista una sesión autenticada y que el usuario tenga el rol requerido. */
function exigirRolApi(string $rol): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $resultado = verificarPermisoApi($rol, $_SESSION);

    if ($resultado === 401) {
        responderProblema(
            401,
            'No autenticado',
            'Debes iniciar sesión para utilizar este recurso.',
            '/errors/no-autenticado'
        );
    }

    if ($resultado === 403) {
        responderProblema(
            403,
            'Acceso denegado',
            'No tienes permisos para utilizar este recurso.',
            '/errors/acceso-denegado'
        );
    }
}