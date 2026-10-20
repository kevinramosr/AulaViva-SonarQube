<?php
declare(strict_types=1);

$endpoint = $argv[1] ?? '';
$id = $argv[2] ?? '';

if (!in_array($endpoint, ['detalle', 'alumnos'], true)) {
    exit(2);
}

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/v1/aulas/' . $endpoint . '/';
$_GET['id'] = $id;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION = [
    'usuario_id' => 1,
    'colegio_id' => 10,
    'rol' => 'profesor'
];

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("
    CREATE TABLE aulas (
        id INTEGER PRIMARY KEY,
        nombre TEXT NOT NULL,
        colegio_id INTEGER NOT NULL,
        activo INTEGER NOT NULL
    );

    CREATE TABLE aula_profesores (
        aula_id INTEGER NOT NULL,
        profesor_id INTEGER NOT NULL
    );

    CREATE TABLE usuarios (
        id INTEGER PRIMARY KEY,
        colegio_id INTEGER NOT NULL,
        rut TEXT,
        username TEXT,
        nombre TEXT,
        apellido TEXT,
        rol TEXT,
        activo INTEGER NOT NULL
    );

    CREATE TABLE aula_alumnos (
        aula_id INTEGER NOT NULL,
        alumno_id INTEGER NOT NULL
    );

    INSERT INTO aulas VALUES
        (1, 'Matemáticas', 10, 1),
        (2, 'Inactiva', 10, 0),
        (3, 'Otro colegio', 20, 1);

    INSERT INTO aula_profesores VALUES (1, 1), (2, 1), (3, 1);

    INSERT INTO usuarios VALUES
        (11, 10, '11111111-1', 'zeta', 'Ana', 'Zúñiga', 'alumno', 1),
        (12, 10, '22222222-2', 'alfa', 'Luis', 'Alonso', 'alumno', 1),
        (13, 10, '33333333-3', 'inactivo', 'Eva', 'Pérez', 'alumno', 0),
        (14, 20, '44444444-4', 'otro', 'Juan', 'Bravo', 'alumno', 1);

    INSERT INTO aula_alumnos VALUES
        (1, 11), (1, 12), (1, 13), (1, 14);
");

ob_start();

if ($endpoint === 'detalle') {
    require __DIR__ . '/../../api/v1/aulas/detalle/index.php';
} else {
    require __DIR__ . '/../../api/v1/aulas/alumnos/index.php';
}

$salida = ob_get_clean();
$respuesta = json_decode($salida, true);

if (!is_array($respuesta)) {
    fwrite(STDERR, 'El endpoint no devolvió JSON válido.');
    exit(3);
}

$respuesta['_http_status'] = http_response_code();

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
