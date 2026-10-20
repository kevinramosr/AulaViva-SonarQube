<?php

require_once __DIR__ . '/config/rol.php';
require_once __DIR__ . '/config/conexion.php';

/* SOLO PROFESORES */

exigirRol('profesor');

/* VALIDAR ID DEL AULA */

$aulaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$aulaId || $aulaId < 1) {
    http_response_code(400);
    exit('Aula inválida.');
}

$profesorId = (int) $_SESSION['usuario_id'];
$colegioId = (int) $_SESSION['colegio_id'];

try {

    /* COMPROBAR QUE EL PROFESOR PERTENECE AL AULA */

    $stmt = $pdo->prepare(
        'SELECT a.id, a.nombre
         FROM aulas AS a
         INNER JOIN aula_profesores AS ap
             ON ap.aula_id = a.id
         WHERE a.id = :aula_id
           AND a.colegio_id = :colegio_id
           AND ap.profesor_id = :profesor_id
           AND a.activo = TRUE
         LIMIT 1'
    );

    $stmt->execute([
        ':aula_id' => $aulaId,
        ':colegio_id' => $colegioId,
        ':profesor_id' => $profesorId
    ]);

    $aula = $stmt->fetch();

    if (!$aula) {
        http_response_code(403);
        exit('No tienes permiso para acceder a esta aula.');
    }

    /* OBTENER ALUMNOS DEL AULA */

    $stmt = $pdo->prepare(
        'SELECT u.id, u.rut, u.username, u.nombre, u.apellido
         FROM usuarios AS u
         INNER JOIN aula_alumnos AS aa
             ON aa.alumno_id = u.id
         WHERE aa.aula_id = :aula_id
           AND u.colegio_id = :colegio_id
           AND u.rol = :rol
           AND u.activo = TRUE
         ORDER BY u.apellido ASC, u.nombre ASC'
    );

    $stmt->execute([
        ':aula_id' => $aulaId,
        ':colegio_id' => $colegioId,
        ':rol' => 'alumno'
    ]);

    $alumnos = $stmt->fetchAll();

} catch (PDOException $e) {

    http_response_code(500);
    exit('No se pudo cargar el aula.');
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($aula['nombre'], ENT_QUOTES, 'UTF-8') ?> | Aula Viva</title>
</head>

<body>

    <p>
        <a href="mis_aulas.php">← Volver a mis aulas</a>
    </p>

    <h1>
        <?= htmlspecialchars($aula['nombre'], ENT_QUOTES, 'UTF-8') ?>
    </h1>

    <h2>Alumnos</h2>

    <?php if (!$alumnos): ?>

        <p>No hay alumnos asignados a esta aula.</p>

    <?php else: ?>

        <?php foreach ($alumnos as $alumno): ?>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $alumno['nombre'] . ' ' . $alumno['apellido'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <p>
                    Usuario:
                    <?= htmlspecialchars(
                        $alumno['username'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="logout.php">Cerrar sesión</a>
    </p>

</body>

</html>