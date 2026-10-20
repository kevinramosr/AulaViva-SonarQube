<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class AulasAlumnosApiCoberturaTest extends TestCase
{
    private function crearBaseDeDatos(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec(
            'CREATE TABLE aulas (
                id INTEGER PRIMARY KEY,
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
            'CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                rut TEXT,
                username TEXT,
                nombre TEXT,
                apellido TEXT,
                rol TEXT,
                activo INTEGER
            )'
        );

        $pdo->exec(
            'CREATE TABLE aula_alumnos (
                aula_id INTEGER,
                alumno_id INTEGER
            )'
        );

        $pdo->exec(
            'INSERT INTO aulas VALUES
                (1, 1, 1),
                (2, 1, 1),
                (3, 2, 1),
                (4, 1, 0)'
        );

        $pdo->exec(
            'INSERT INTO aula_profesores VALUES
                (1, 10),
                (2, 20),
                (3, 10),
                (4, 10)'
        );

        $pdo->exec(
            "INSERT INTO usuarios VALUES
                (101, 1, '11111111-1', 'ana', 'Ana', 'Zapata', 'alumno', 1),
                (102, 1, '22222222-2', 'pedro', 'Pedro', 'Alvarez', 'alumno', 1),
                (103, 1, '33333333-3', 'inactivo', 'Luis', 'Torres', 'alumno', 0),
                (104, 2, '44444444-4', 'externo', 'Mario', 'Fuentes', 'alumno', 1),
                (105, 1, '55555555-5', 'profesor', 'Juan', 'Pérez', 'profesor', 1)"
        );

        $pdo->exec(
            'INSERT INTO aula_alumnos VALUES
                (1, 101),
                (1, 102),
                (1, 103),
                (1, 104),
                (1, 105)'
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
        $_SERVER['REQUEST_URI'] = '/api/v1/aulas/alumnos/';

        $_GET = ['id' => $idAula];

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [
            'usuario_id' => $profesorId,
            'colegio_id' => $colegioId,
            'rol' => $rol
        ];

        $pdo = $provocarErrorBD
            ? new PDO('sqlite::memory:')
            : $this->crearBaseDeDatos();

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        ob_start();

        try {
            require __DIR__ . '/../api/v1/aulas/alumnos/index.php';
        } catch (RespuestaApiTerminada $e) {
            // Captura la terminación controlada de errores HTTP.
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
        $this->assertSame('Método no permitido', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaUsuarioQueNoEsProfesor(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'alumno');

        $this->assertSame(403, $r['status']);
        $this->assertSame('Acceso denegado', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaIdInvalido(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 'abc');

        $this->assertSame(400, $r['status']);
        $this->assertSame('Solicitud inválida', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaDeOtroProfesor(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 2);

        $this->assertSame(403, $r['status']);
        $this->assertSame('Acceso denegado', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaDeOtroColegio(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 3);

        $this->assertSame(403, $r['status']);
        $this->assertSame('Acceso denegado', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaAulaInactiva(): void
    {
        $r = $this->ejecutarSolicitud('GET', 'profesor', 4);

        $this->assertSame(403, $r['status']);
        $this->assertSame('Acceso denegado', $r['json']['title']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDevuelveSoloAlumnosActivosDelColegio(): void
    {
        $r = $this->ejecutarSolicitud();

        $this->assertSame(200, $r['status']);
        $this->assertCount(2, $r['json']['data']);

        // La consulta ordena por apellido.
        $this->assertSame('pedro', $r['json']['data'][0]['username']);
        $this->assertSame('ana', $r['json']['data'][1]['username']);

        $this->assertSame(
            ['id', 'rut', 'username', 'nombre', 'apellido'],
            array_keys($r['json']['data'][0])
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDevuelveListaVaciaSiNoHayAlumnos(): void
    {
        // El profesor 20 tiene acceso al aula 2, sin alumnos.
        $r = $this->ejecutarSolicitud(
            'GET',
            'profesor',
            2,
            20
        );

        $this->assertSame(200, $r['status']);
        $this->assertSame([], $r['json']['data']);
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
