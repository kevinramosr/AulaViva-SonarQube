<?php
declare(strict_types=1);

// Este servidor es exclusivamente para pruebas locales.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit;
}

// No permitir que el servidor ejecute otras rutas.
if (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) !== '/api/v1/auth/login/') {
    http_response_code(404);
    exit;
}

// Base de datos temporal: NO utiliza Supabase.
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(
    'CREATE TABLE usuarios (
        id INTEGER PRIMARY KEY,
        colegio_id INTEGER,
        rut TEXT,
        username TEXT,
        nombre TEXT,
        apellido TEXT,
        password_hash TEXT,
        rol TEXT,
        activo INTEGER
    )'
);

// Usuario ficticio para las pruebas.
$stmt = $pdo->prepare(
    'INSERT INTO usuarios
     (id, colegio_id, rut, username, nombre, apellido,
      password_hash, rol, activo)
     VALUES
     (1, 10, :rut, :username, :nombre, :apellido,
      :password_hash, :rol, 1)'
);

$stmt->execute([
    ':rut' => '11111111-1',
    ':username' => 'usuario_prueba',
    ':nombre' => 'Usuario',
    ':apellido' => 'Prueba',
    ':password_hash' => password_hash('ClaveDePrueba123', PASSWORD_DEFAULT),
    ':rol' => 'profesor'
]);

// El endpoint recibe $pdo y no carga la conexión real.
require __DIR__ . '/../../api/v1/auth/login/index.php';
