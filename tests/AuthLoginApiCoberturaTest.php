<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class AuthLoginApiCoberturaTest extends TestCase
{
    private function crearBaseDeDatos(): PDO
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                rut TEXT,
                username TEXT,
                nombre TEXT,
                apellido TEXT,
                password_hash TEXT,
                rol TEXT,
                activo INTEGER
            )'
        );

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios
                (id, colegio_id, rut, username, nombre,
                 apellido, password_hash, rol, activo)
             VALUES
                (:id, 1, :rut, :username, :nombre,
                 :apellido, :password_hash, :rol, :activo)'
        );

        $stmt->execute([
            ':id' => 10,
            ':rut' => '12345678-9',
            ':username' => 'profesor1',
            ':nombre' => 'Juan',
            ':apellido' => 'Pérez',
            ':password_hash' => password_hash(
                'ClaveSegura123',
                PASSWORD_DEFAULT
            ),
            ':rol' => 'profesor',
            ':activo' => 1
        ]);

        $stmt->execute([
            ':id' => 11,
            ':rut' => '98765432-1',
            ':username' => 'profesor_inactivo',
            ':nombre' => 'Pedro',
            ':apellido' => 'Gómez',
            ':password_hash' => password_hash(
                'ClaveSegura123',
                PASSWORD_DEFAULT
            ),
            ':rol' => 'profesor',
            ':activo' => 0
        ]);

        return $pdo;
    }

    private function ejecutarSolicitud(
        string $metodo = 'POST',
        string $json = '{}',
        bool $provocarErrorBD = false
    ): array {
        define('PHPUNIT_API_TEST', true);

        $_SERVER['REQUEST_METHOD'] = $metodo;
        $_SERVER['REQUEST_URI'] = '/api/v1/auth/login/';

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        $pdo = $provocarErrorBD
            ? new PDO('sqlite::memory:')
            : $this->crearBaseDeDatos();

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $contenidoPrueba = $json;

        ob_start();

        try {
            require __DIR__ . '/../api/v1/auth/login/index.php';
        } catch (RespuestaApiTerminada $e) {
            // Capturamos la terminación HTTP de la respuesta de error.
        } finally {
            $salida = ob_get_clean();
        }

        return [
            'status' => http_response_code(),
            'json' => json_decode($salida, true),
            'sesion' => $_SESSION
        ];
    }

    private function jsonCredenciales(
        string $identificador,
        string $password
    ): string {
        return json_encode([
            'identificador' => $identificador,
            'password' => $password
        ], JSON_THROW_ON_ERROR);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaMetodoGet(): void
    {
        $respuesta = $this->ejecutarSolicitud('GET');

        $this->assertSame(405, $respuesta['status']);
        $this->assertSame(
            'Método no permitido',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaJsonInvalido(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            '{json incorrecto'
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Solicitud inválida',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaDatosIncompletos(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            '{"identificador":"profesor1"}'
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Datos incompletos',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaUsuarioInexistente(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            $this->jsonCredenciales(
                'usuario_inexistente',
                'ClaveSegura123'
            )
        );

        $this->assertSame(401, $respuesta['status']);
        $this->assertSame(
            'Credenciales inválidas',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaUsuarioInactivo(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            $this->jsonCredenciales(
                'profesor_inactivo',
                'ClaveSegura123'
            )
        );

        $this->assertSame(401, $respuesta['status']);
        $this->assertSame(
            'Credenciales inválidas',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaPasswordIncorrecta(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            $this->jsonCredenciales(
                'profesor1',
                'ClaveIncorrecta'
            )
        );

        $this->assertSame(401, $respuesta['status']);
        $this->assertSame(
            'Credenciales inválidas',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginCorrecto(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            $this->jsonCredenciales(
                'profesor1',
                'ClaveSegura123'
            )
        );

        $this->assertSame(200, $respuesta['status']);

        $this->assertSame(
            'profesor1',
            $respuesta['json']['data']['username']
        );

        $this->assertSame(
            10,
            $respuesta['sesion']['usuario_id']
        );

        $this->assertSame(
            'profesor',
            $respuesta['sesion']['rol']
        );

        $this->assertSame(
            1,
            $respuesta['sesion']['colegio_id']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testErrorDeBaseDeDatos(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            $this->jsonCredenciales(
                'profesor1',
                'ClaveSegura123'
            ),
            true
        );

        $this->assertSame(500, $respuesta['status']);
        $this->assertSame(
            'Error interno',
            $respuesta['json']['title']
        );
    }
}
