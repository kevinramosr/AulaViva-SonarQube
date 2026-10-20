<?php

require_once __DIR__ . '/../_helpers/api.php';
require_once __DIR__ . '/../../../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    responderProblema(
        405,
        'Método no permitido',
        'Este recurso solamente admite solicitudes POST.',
        '/errors/metodo-no-permitido'
    );
}

exigirRolApi('profesor');

$colegioId = (int) $_SESSION['colegio_id'];


/*
|--------------------------------------------------------------------------
| Idempotency-Key
|--------------------------------------------------------------------------
*/

$idempotencyKey = trim($_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? '');

if ($idempotencyKey === '') {

    responderProblema(
        400,
        'Idempotency-Key requerido',
        'Debes enviar el encabezado Idempotency-Key.',
        '/errors/idempotency-key-requerido'
    );
}

if (strlen($idempotencyKey) > 100) {

    responderProblema(
        400,
        'Idempotency-Key inválido',
        'Idempotency-Key no puede superar los 100 caracteres.',
        '/errors/idempotency-key-invalido'
    );
}


/*
|--------------------------------------------------------------------------
| Leer JSON
|--------------------------------------------------------------------------
*/

$contenido = file_get_contents('php://input');
$datos = json_decode($contenido, true);

if (!is_array($datos)) {

    responderProblema(
        400,
        'JSON inválido',
        'El cuerpo de la solicitud debe contener JSON válido.',
        '/errors/json-invalido'
    );
}

$email = trim($datos['email'] ?? '');
$rut = trim($datos['rut'] ?? '');
$username = trim($datos['username'] ?? '');
$rol = trim($datos['rol'] ?? '');


/*
|--------------------------------------------------------------------------
| Validaciones
|--------------------------------------------------------------------------
*/

if ($email === '' || $rut === '' || $username === '' || $rol === '') {

    responderProblema(
        400,
        'Datos incompletos',
        'email, rut, username y rol son obligatorios.',
        '/errors/datos-incompletos'
    );
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    responderProblema(
        400,
        'Email inválido',
        'El email no tiene un formato válido.',
        '/errors/email-invalido'
    );
}

if (!in_array($rol, ['alumno', 'profesor'], true)) {

    responderProblema(
        400,
        'Rol inválido',
        'El rol debe ser alumno o profesor.',
        '/errors/rol-invalido'
    );
}


try {

    /*
    |--------------------------------------------------------------------------
    | ¿Esta operación ya se realizó?
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare(
        'SELECT
            id,
            email,
            rut,
            username,
            rol,
            expira_en
         FROM invitaciones
         WHERE colegio_id = :colegio_id
           AND idempotency_key = :idempotency_key
         LIMIT 1'
    );

    $stmt->execute([
        ':colegio_id' => $colegioId,
        ':idempotency_key' => $idempotencyKey
    ]);

    $existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existente) {

        http_response_code(200);

        header('Idempotent-Replayed: true');

        echo json_encode([
            'data' => [
                'id' => (int) $existente['id'],
                'email' => $existente['email'],
                'rut' => $existente['rut'],
                'username' => $existente['username'],
                'rol' => $existente['rol'],
                'expira_en' => $existente['expira_en']
            ],
            'meta' => [
                'idempotent_replay' => true
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Evitar invitaciones activas duplicadas
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare(
        'SELECT id
         FROM invitaciones
         WHERE colegio_id = :colegio_id
           AND email = :email
           AND usado = FALSE
           AND expira_en > NOW()
         LIMIT 1'
    );

    $stmt->execute([
        ':colegio_id' => $colegioId,
        ':email' => $email
    ]);

    if ($stmt->fetch()) {

        responderProblema(
            409,
            'Invitación duplicada',
            'Ya existe una invitación activa para este email.',
            '/errors/invitacion-duplicada'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Crear invitación
    |--------------------------------------------------------------------------
    */

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);

    // PostgreSQL calculará el vencimiento de forma consistente.
    $stmt = $pdo->prepare(
        "INSERT INTO invitaciones
            (
                colegio_id,
                email,
                rut,
                username,
                token_hash,
                rol,
                expira_en,
                idempotency_key
            )
         VALUES
            (
                :colegio_id,
                :email,
                :rut,
                :username,
                :token_hash,
                :rol,
                NOW() + INTERVAL '24 hours',
                :idempotency_key
            )
         RETURNING
            id,
            email,
            rut,
            username,
            rol,
            expira_en"
    );

    $stmt->execute([
        ':colegio_id' => $colegioId,
        ':email' => $email,
        ':rut' => $rut,
        ':username' => $username,
        ':token_hash' => $tokenHash,
        ':rol' => $rol,
        ':idempotency_key' => $idempotencyKey
    ]);

    $invitacion = $stmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);

    echo json_encode([
        'data' => [
            'id' => (int) $invitacion['id'],
            'email' => $invitacion['email'],
            'rut' => $invitacion['rut'],
            'username' => $invitacion['username'],
            'rol' => $invitacion['rol'],
            'expira_en' => $invitacion['expira_en'],
            'url_activacion' =>
                '/activar_cuenta.php?token=' . urlencode($token)
        ],
        'meta' => [
            'idempotent_replay' => false
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {

    // PostgreSQL 23505 = violación de restricción UNIQUE.
    if ($e->getCode() === '23505') {

        responderProblema(
            409,
            'Conflicto',
            'La operación entra en conflicto con un recurso existente.',
            '/errors/conflicto'
        );
    }

    responderProblema(
        500,
        'Error interno',
        'No se pudo crear la invitación.',
        '/errors/error-interno'
    );
}