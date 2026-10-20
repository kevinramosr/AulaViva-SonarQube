<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class AulasDetalleCoberturaTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(<<<'SQL'
CREATE TABLE aulas (id INTEGER PRIMARY KEY, nombre TEXT NOT NULL, colegio_id INTEGER NOT NULL, activo INTEGER NOT NULL);
CREATE TABLE aula_profesores (aula_id INTEGER NOT NULL, profesor_id INTEGER NOT NULL);
CREATE TABLE usuarios (id INTEGER PRIMARY KEY, colegio_id INTEGER NOT NULL, rut TEXT, username TEXT, nombre TEXT, apellido TEXT, rol TEXT, activo INTEGER NOT NULL);
CREATE TABLE aula_alumnos (aula_id INTEGER NOT NULL, alumno_id INTEGER NOT NULL);
INSERT INTO aulas VALUES (1, 'Matemáticas', 10, 1), (2, 'Inactiva', 10, 0), (3, 'Otro colegio', 20, 1);
INSERT INTO aula_profesores VALUES (1, 1), (2, 1), (3, 1);
INSERT INTO usuarios VALUES
(11, 10, '11111111-1', 'zeta', 'Ana', 'Zúñiga', 'alumno', 1),
(12, 10, '22222222-2', 'alfa', 'Luis', 'Alonso', 'alumno', 1),
(13, 10, '33333333-3', 'inactivo', 'Eva', 'Pérez', 'alumno', 0),
(14, 20, '44444444-4', 'otro', 'Juan', 'Bravo', 'alumno', 1);
INSERT INTO aula_alumnos VALUES (1, 11), (1, 12), (1, 13), (1, 14);
SQL);
    }

    private function prepararSolicitud(string $ruta): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $ruta;
        $_GET['id'] = '1';
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = ['usuario_id' => 1, 'colegio_id' => 10, 'rol' => 'profesor'];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDetalleDevuelveAulaAutorizada(): void
    {
        $pdo = $this->pdo;
        $this->prepararSolicitud('/api/v1/aulas/detalle/?id=1');
        ob_start();
        try {
            require __DIR__ . '/../api/v1/aulas/detalle/index.php';
            $salida = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $respuesta = json_decode((string) $salida, true);
        $this->assertIsArray($respuesta);
        $this->assertSame(1, (int) $respuesta['data']['id']);
        $this->assertSame('Matemáticas', $respuesta['data']['nombre']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAlumnosDevuelveSoloAlumnosActivos(): void
    {
        $pdo = $this->pdo;
        $this->prepararSolicitud('/api/v1/aulas/alumnos/?id=1');
        ob_start();
        try {
            require __DIR__ . '/../api/v1/aulas/alumnos/index.php';
            $salida = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $respuesta = json_decode((string) $salida, true);
        $this->assertIsArray($respuesta);
        $this->assertCount(2, $respuesta['data']);
        $this->assertSame(['Alonso', 'Zúñiga'], array_column($respuesta['data'], 'apellido'));
    }
}
