<?php

require_once __DIR__ . '/../../_helpers/api.php';

// Permite inyectar una conexión de prueba sin acceder a Supabase.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    require_once __DIR__ . '/../../../../config/conexion.php';
}

header('Content-Type: application/json; charset=utf-8');

/*
|--------------------------------------------------------------------------
| GET /api/v1/aulas/{aulaId}
|--------------------------------------------------------------------------
| Devuelve la información de un aula a la que pertenece el profesor.
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

// Solo profesores autenticados
exigirRolApi('profesor');

// Validar ID recibido por la URL
$idRecibido = PHP_SAPI === 'cli'
    ? ($_GET['id'] ?? null)
    : filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$aulaId = filter_var($idRecibido, FILTER_VALIDATE_INT);

if (!$aulaId || $aulaId < 1) {
    responderProblema(
        400,
        'Solicitud inválida',
        'El ID del aula debe ser un número entero mayor que 0.',
        '/errors/id-aula-invalido'
    );
}

$profesorId = (int) $_SESSION['usuario_id'];
$colegioId = (int) $_SESSION['colegio_id'];

try {

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

    $aula = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aula) {
        responderProblema(
            403,
            'Acceso denegado',
            'No tienes permiso para acceder a esta aula.',
            '/errors/acceso-denegado'
        );
    }

    http_response_code(200);

    echo json_encode([
        'data' => $aula
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    responderProblema(
        500,
        'Error interno del servidor',
        'No se pudo cargar el aula.',
        '/errors/error-interno'
    );
}