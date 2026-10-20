<?php
declare(strict_types=1);

// Servidor exclusivamente local para pruebas.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit;
}

$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($ruta !== '/api/v1/aulas/') {
    http_response_code(404);
    exit;
}

// Base de datos SQLite en memoria. Nunca conecta a Supabase.
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(
    'CREATE TABLE aulas (
        id INTEGER PRIMARY KEY,
        nombre TEXT NOT NULL,
        colegio_id INTEGER NOT NULL,
        activo INTEGER NOT NULL
    )'
);

$pdo->exec(
    'CREATE TABLE aula_profesores (
        aula_id INTEGER NOT NULL,
        profesor_id INTEGER NOT NULL
    )'
);

// Datos ficticios: dos aulas activas y una inactiva.
$pdo->exec("
    INSERT INTO aulas (id, nombre, colegio_id, activo) VALUES
    (1, 'Matemáticas', 10, 1),
    (2, 'Ciencias', 10, 1),
    (3, 'Aula inactiva', 10, 0),
    (4, 'Otro colegio', 20, 1)
");

$pdo->exec("
    INSERT INTO aula_profesores (aula_id, profesor_id) VALUES
    (1, 1),
    (2, 1),
    (3, 1),
    (4, 1)
");

// Simular la sesión de un profesor ficticio.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['usuario_id'] = 1;
$_SESSION['colegio_id'] = 10;
$_SESSION['rol'] = 'profesor';

// Ejecutar el endpoint real con la conexión de prueba.
require __DIR__ . '/../../api/v1/aulas/index.php';
