<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class AulasDetalleApiCoberturaTest extends TestCase
{
    private function crearBaseDeDatos(): PDO
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE aulas (
                id INTEGER PRIMARY KEY,
                nombre TEXT,
                colegio_id INTEGER,
                activo INTEGER
            )'
        );

        $pdo->exec(
            'CREATE TABLE aula_profesores (
                aula_id INTEGER,
                profesor_id INTEGER
            )'
        );

        $pdo->exec(
            "INSERT INTO aulas VALUES
                (1, 'Matemáticas', 1, 1),
                (2, 'Lenguaje', 1, 1),
                (3, 'Aula de otro colegio', 2, 1),
                (4, 'Aula inactiva', 1, 0),
                (5, 'Aula de otro profesor', 1, 1)"
        );

        $pdo->exec(
            'INSERT INTO aula_profesores VALUES
                (1, 10),
                (2, 10),
                (3, 10),
                (4, 10),
                (5, 20)'
        );

        return $pdo;
    }

    private function ejecutarSolicitud(
        string $metodo = 'GET',
        string $rol = 'profesor',
        mixed $idAula = 1,
        int $profesorId = 10,
        int $colegioId = 1,
        bool $provocarErrorBD = false
    ): array {
        define('PHPUNIT_API_TEST', true);

        $_SERVER['REQUEST_METHOD'] = $metodo;
        $_SERVER['REQUEST_URI'] = '/api/v1/aulas/detalle/';

        $_GET = ['id' => $idAula];

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [
            'usuario_id' => $profesorId,
            'colegio_id' => $colegioId,
            'rol' => $rol
        ];

        // Nunca utiliza la conexión real de Supabase.
        $pdo = $provocarErrorBD
            ? new PDO('sqlite::memory:')
            : $this->crearBaseDeDatos();

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        ob_start();

        try {
            require __DIR__ . '/../api/v1/aulas/detalle/index.php';
        } catch (RespuestaApiTerminada $e) {
            // El endpoint terminó de construir su respuesta de error.
        } finally {
            $salida = ob_get_clean();
        }

        return [
            'status' => http_response_code(),
            'json' => json_decode($salida, true)
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaMetodoPost(): void
    {
        $r = $this->ejecutarSolicitud('POST');

        $this->assertSame(405, $r['status']);
        $this->assertSame(
            'Método no permitido',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaUsuarioQueNoEsProfesor(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'alumno');

        $this->assertSame(403, $r['status']);
        $this->assertSame(
            'Acceso denegado',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaIdNoNumerico(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 'abc');

        $this->assertSame(400, $r['status']);
        $this->assertSame(
            'Solicitud inválida',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaIdCero(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 0);

        $this->assertSame(400, $r['status']);
        $this->assertSame(
            '/errors/id-aula-invalido',
            $r['json']['type']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaDeOtroProfesor(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 5);

        $this->assertSame(403, $r['status']);
        $this->assertSame(
            'Acceso denegado',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaDeOtroColegio(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 3);

        $this->assertSame(403, $r['status']);
        $this->assertSame(
            'Acceso denegado',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaInactiva(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 4);

        $this->assertSame(403, $r['status']);
        $this->assertSame(
            'Acceso denegado',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDevuelveDetalleDelAula(): void
    {
        $r = $this->ejecutarSolicitud();

        $this->assertSame(200, $r['status']);

        $this->assertSame(
            1,
            (int) $r['json']['data']['id']
        );

        $this->assertSame(
            'Matemáticas',
            $r['json']['data']['nombre']
        );

        $this->assertCount(2, $r['json']['data']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDevuelveOtraAulaAsignada(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 2);

        $this->assertSame(200, $r['status']);
        $this->assertSame(
            'Lenguaje',
            $r['json']['data']['nombre']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaInexistente(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 999);

        $this->assertSame(403, $r['status']);
        $this->assertSame(
            'Acceso denegado',
            $r['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRespondeError500SiFallaBaseDeDatos(): void
    {
        $r = $this->ejecutarSolicitud(
            'GET',
            'profesor',
            1,
            10,
            1,
            true
        );

        $this->assertSame(500, $r['status']);
        $this->assertSame(
            'Error interno del servidor',
            $r['json']['title']
        );
    }
}
