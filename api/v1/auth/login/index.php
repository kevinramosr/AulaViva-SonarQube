<?php

require_once __DIR__ . '/../../_helpers/api.php';
// Permite utilizar una conexión de prueba sin acceder a Supabase.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    require_once __DIR__ . '/../../../../config/conexion.php';
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    responderProblema(
        405,
        'Método no permitido',
        'Este recurso solamente admite solicitudes POST.',
        '/errors/metodo-no-permitido'
    );
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Permitir JSON simulado únicamente durante las pruebas CLI.
$contenido = (
    PHP_SAPI === 'cli' && isset($contenidoPrueba)
)
    ? $contenidoPrueba
    : file_get_contents('php://input');

$datos = json_decode($contenido, true);

if (!is_array($datos)) {

    responderProblema(
        400,
        'Solicitud inválida',
        'El cuerpo de la solicitud debe contener JSON válido.',
        '/errors/json-invalido'
    );
}

$identificador = trim($datos['identificador'] ?? '');
$password = $datos['password'] ?? '';

if ($identificador === '' || $password === '') {

    responderProblema(
        400,
        'Datos incompletos',
        'Los campos identificador y password son obligatorios.',
        '/errors/datos-incompletos'
    );
}

try {

    $stmt = $pdo->prepare(
        'SELECT
            id,
            colegio_id,
            rut,
            username,
            nombre,
            apellido,
            password_hash,
            rol,
            activo
         FROM usuarios
         WHERE rut = :rut
            OR username = :username
         LIMIT 1'
    );

    $stmt->execute([
        ':rut' => $identificador,
        ':username' => $identificador
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        !$usuario ||
        !$usuario['activo'] ||
        !password_verify($password, $usuario['password_hash'])
    ) {

        responderProblema(
            401,
            'Credenciales inválidas',
            'El usuario o la contraseña son incorrectos.',
            '/errors/credenciales-invalidas'
        );
    }

    session_regenerate_id(true);

    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['colegio_id'] = (int) $usuario['colegio_id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    http_response_code(200);

    echo json_encode([
        'data' => [
            'id' => (int) $usuario['id'],
            'username' => $usuario['username'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'rol' => $usuario['rol']
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    responderProblema(
        500,
        'Error interno',
        'No se pudo iniciar sesión.',
        '/errors/error-interno'
    );
}