<?php
declare(strict_types=1);

/**
 * Valida los datos necesarios para crear una invitación.
 * Devuelve null si son válidos o un error si no lo son.
 */
function validarDatosInvitacion(array $datos): ?array
{
    $email = trim((string) ($datos['email'] ?? ''));
    $rut = trim((string) ($datos['rut'] ?? ''));
    $username = trim((string) ($datos['username'] ?? ''));
    $rol = trim((string) ($datos['rol'] ?? ''));

    if (
        $email === '' ||
        $rut === '' ||
        $username === '' ||
        $rol === ''
    ) {
        return [
            'status' => 400,
            'title' => 'Datos incompletos',
            'detail' => 'email, rut, username y rol son obligatorios.',
            'type' => '/errors/datos-incompletos'
        ];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 400,
            'title' => 'Email inválido',
            'detail' => 'El email no tiene un formato válido.',
            'type' => '/errors/email-invalido'
        ];
    }

    if (!in_array($rol, ['alumno', 'profesor'], true)) {
        return [
            'status' => 400,
            'title' => 'Rol inválido',
            'detail' => 'El rol debe ser alumno o profesor.',
            'type' => '/errors/rol-invalido'
        ];
    }

    return null;
}
