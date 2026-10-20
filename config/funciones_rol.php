<?php
declare(strict_types=1);

/**
 * Determina el código de error de una autorización.
 *
 * null = acceso permitido
 * 403  = acceso denegado
 * 500  = configuración inválida
 */
function verificarPermisoRol(string $rol, ?string $rolUsuario): ?int
{
    $rolesPermitidos = ['alumno', 'profesor', 'admin'];

    if (!in_array($rol, $rolesPermitidos, true)) {
        return 500;
    }

    if ($rolUsuario !== $rol) {
        return 403;
    }

    return null;
}

function exigirRol(string $rol): void
{
    $rolUsuario = $_SESSION['rol'] ?? null;

    $resultado = verificarPermisoRol($rol, $rolUsuario);

    if ($resultado === 500) {
        http_response_code(500);
        exit('Configuración de rol inválida.');
    }

    if ($resultado === 403) {
        http_response_code(403);
        exit('No tienes permiso para acceder a esta página.');
    }
}