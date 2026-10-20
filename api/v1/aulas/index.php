<?php

require_once __DIR__ . '/../_helpers/api.php';

// Permite inyectar una conexión de prueba sin acceder a Supabase.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    require_once __DIR__ . '/../../../config/conexion.php';
}


header('Content-Type: application/json; charset=utf-8');

/*
|--------------------------------------------------------------------------
| GET /api/v1/aulas
|--------------------------------------------------------------------------
| Devuelve las aulas activas asignadas al profesor autenticado.
*/

// Este endpoint solo acepta GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderProblema(
        405,
        'Método no permitido',
        'Este recurso solo permite solicitudes GET.',
        '/errors/metodo-no-permitido'
    );
}

// Solo profesores
exigirRolApi('profesor');

// Datos del profesor autenticado
$profesorId = (int) $_SESSION['usuario_id'];
$colegioId = (int) $_SESSION['colegio_id'];

try {

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

    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);

    echo json_encode([
        'data' => $aulas
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    responderProblema(
        500,
        'Error interno del servidor',
        'No se pudieron cargar las aulas.',
        '/errors/error-interno'
    );
}