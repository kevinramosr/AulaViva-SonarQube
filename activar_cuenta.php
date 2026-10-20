<?php

require_once __DIR__ . '/config/conexion.php';

$token = $_GET['token'] ?? '';

if ($token === '') {
    exit('Invitación no válida.');
}

/* Convertimos el token recibido al mismo hash guardado en la base */

$token_hash = hash('sha256', $token);

$stmt = $pdo->prepare(
    'SELECT id, colegio_id, email, rut, username, rol, expira_en, usado
     FROM invitaciones
     WHERE token_hash = :token_hash
     LIMIT 1'
);

$stmt->execute([
    ':token_hash' => $token_hash
]);

$invitacion = $stmt->fetch();

if (!$invitacion) {
    exit('Invitación no válida.');
}

if ($invitacion['usado']) {
    exit('Esta invitación ya fue utilizada.');
}

if (strtotime($invitacion['expira_en']) < time()) {
    exit('Esta invitación ha expirado.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';

    if (
        $nombre === '' ||
        $apellido === '' ||
        $username === '' ||
        $password === '' ||
        $confirmar_password === ''
    ) {
        $error = 'Debes completar todos los campos.';
    } elseif ($password !== $confirmar_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    }
    else {

    $stmt = $pdo->prepare(
        'SELECT id
         FROM usuarios
         WHERE rut = :rut OR username = :username
         LIMIT 1'
    );

    $stmt->execute([
        ':rut' => $invitacion['rut'],
        ':username' => $username
    ]);

    if ($stmt->fetch()) {
        $error = 'El RUT o nombre de usuario ya está registrado.';
    }
}
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | AulaIA</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <header>
        <img src="img/Logo.png" alt="Logo AulaIA" class="logo-header">
        <h1>AulaIA</h1>
    </header>

    <main>

        <div class="login">

            <img src="img/Logo.png" alt="Logo AulaIA" class="logo-login">

            <h1>Crear cuenta</h1>

            <p class="subtitulo">
                Completa tus datos para activar tu cuenta
            </p>
            <?php if ($error !== ''): ?>

                <p class="error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>

            <?php endif; ?>

            <form method="POST">
                <label for="nombre">Nombre</label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ingresa tu nombre"
                    required>

                <label for="apellido">Apellido</label>

                <input
                    type="text"
                    id="apellido"
                    name="apellido"
                    placeholder="Ingresa tu apellido"
                    required>

                <label>Correo electrónico</label>

                <input
                    type="email"
                    value="<?= htmlspecialchars($invitacion['email'], ENT_QUOTES, 'UTF-8') ?>"
                    disabled>

                <label for="username">Nombre de usuario</label>

                <input
                    type="text"
                    value="<?= htmlspecialchars($invitacion['username'], ENT_QUOTES, 'UTF-8') ?>"
                    disabled>

                <label for="password">Contraseña</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Crea una contraseña"
                    required>

                <label for="confirmar_password">Confirmar contraseña</label>

                <input
                    type="password"
                    id="confirmar_password"
                    name="confirmar_password"
                    placeholder="Repite tu contraseña"
                    required>

                <button type="submit">Crear cuenta</button>

            </form>

        </div>

    </main>

</body>

</html>