<?php

session_start();

require_once __DIR__ . '/config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identificador = trim($_POST['identificador'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identificador === '' || $password === '') {

        $error = 'Debes completar todos los campos.';

    } else {

        try {

            $stmt = $pdo->prepare(
                'SELECT id, colegio_id, rut, username, nombre,
                        apellido, password_hash, rol, activo
                 FROM usuarios
                 WHERE rut = :rut OR username = :username
                 LIMIT 1'
            );

            $stmt->execute([
                ':rut' => $identificador,
                ':username' => $identificador
            ]);

            $usuario = $stmt->fetch();

            if (
                $usuario &&
                $usuario['activo'] &&
                password_verify($password, $usuario['password_hash'])
            ) {

                session_regenerate_id(true);

                $_SESSION['usuario_id'] = (int) $usuario['id'];
                $_SESSION['colegio_id'] = (int) $usuario['colegio_id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                header('Location: index.html');
                exit;
            }

            $error = 'Usuario o contraseña incorrectos';

        } catch (PDOException $e) {

            $error = 'No se pudo iniciar sesión. Inténtalo nuevamente.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | AulaIA</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="img/Logo.png" alt="Logo aula" class="logo-header">
        <h1>AulaIA</h1>
    </header>
    <main>
        <div class="login">
            <img src="img/Logo.png" alt="Logo AulaIA" class="logo-login">
            <h1>Iniciar sesión</h1>
            <p class="subtitulo">Ingresa a tu aula virtual</p>
            
                        <?php if ($error !== ''): ?>
                <p class="error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>

            <?php endif; ?>
            <form method="POST">
                <label for="identificador">RUT o nombre de usuario</label>
                
                <input 
                type="text" 
                id="identificador" 
                name="identificador" 
                placeholder="Ingresa tu Rut o usuario" 
                required>

                <label for="password">Contraseña</label>

                <input 
                type="password"
                id="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                required>

                <button type="submit">Iniciar sesión</button>

            </form>
            <div class="ayuda">
                <h2>¿No puedes ingresar?</h2>
                <p>Si tienes problemas para acceder, comunícate con el administrador de tu establecimiento.</p>

            </div>
        </div>
    </main>
</body>
</html>