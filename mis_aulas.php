<?php

require_once __DIR__ . '/config/rol.php';
require_once __DIR__ . '/config/conexion.php';

/* SOLO PROFESORES */

exigirRol('profesor');

/* OBTENER DATOS DE LA SESIÓN */

$profesorId = (int) $_SESSION['usuario_id'];
$colegioId = (int) $_SESSION['colegio_id'];

try {

    /* BUSCAR SOLO LAS AULAS ASIGNADAS AL PROFESOR Y A SU COLEGIO */

    $stmt = $pdo->prepare(
        'SELECT a.id, a.nombre
         FROM aulas AS a
         INNER JOIN aula_profesores AS ap
             ON ap.aula_id = a.id
         WHERE ap.profesor_id = :profesor_id
           AND a.colegio_id = :colegio_id
           AND a.activo = TRUE
         ORDER BY a.nombre ASC'
    );

    $stmt->execute([
        ':profesor_id' => $profesorId,
        ':colegio_id' => $colegioId
    ]);

    $aulas = $stmt->fetchAll();

} catch (PDOException $e) {

    http_response_code(500);
    exit('No se pudieron cargar las aulas.');
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis aulas | Aula Viva</title>
</head>

<body>

    <h1>Mis aulas</h1>

    <p>
        Profesor:
        <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <?php if (!$aulas): ?>

        <p>No tienes aulas asignadas.</p>

    <?php else: ?>

        <?php foreach ($aulas as $aula): ?>

            <div>

                <h2>
                    <a href="aula.php?id=<?= (int) $aula['id'] ?>">
                        <?= htmlspecialchars($aula['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="logout.php">Cerrar sesión</a>
    </p>

</body>

</html>