<?php

session_start();

require_once __DIR__ . '/config/conexion.php';

/* Datos de prueba */

$email = 'alumno2@prueba.cl';
$rut = '33333333-3';
$username = 'alumno2';
$rol = 'alumno';
$colegio_id = 1;

/* Token que irá en el enlace */

$token = bin2hex(random_bytes(32));

/* En la base de datos guardamos solamente el hash */

$token_hash = hash('sha256', $token);

/* La invitación durará 24 horas */

$expira_en = date('Y-m-d H:i:sP', time() + 86400);

$stmt = $pdo->prepare(
    'INSERT INTO invitaciones
     (colegio_id, email, rut, username, token_hash, rol, expira_en)
    VALUES
     (:colegio_id, :email, :rut, :username, :token_hash, :rol, :expira_en)'
);

$stmt->execute([
    ':colegio_id' => $colegio_id,
    ':email' => $email,
    ':rut' => $rut,
    ':username' => $username,
    ':token_hash' => $token_hash,
    ':rol' => $rol,
    ':expira_en' => $expira_en
]);

echo 'Invitación creada correctamente.<br><br>';

echo 'Enlace de prueba:<br>';

echo 'http://localhost/Aula%20viva/activar_cuenta.php?token='
    . urlencode($token);