<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class InvitacionesApiCoberturaTest extends TestCase
{
    private function ejecutarSolicitud(
        string $metodo = 'POST',
        string $rol = 'profesor',
        string $idempotencyKey = 'prueba-001',
        string $json = '{}'
    ): array {
        define('PHPUNIT_API_TEST', true);

        $_SERVER['REQUEST_METHOD'] = $metodo;
        $_SERVER['REQUEST_URI'] = '/api/v1/invitaciones/';
        $_SERVER['HTTP_IDEMPOTENCY_KEY'] = $idempotencyKey;

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['usuario_id'] = 10;
        $_SESSION['colegio_id'] = 1;
        $_SESSION['rol'] = $rol;

        // Conexión aislada, sin acceder a Supabase.
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        // JSON proporcionado por PHPUnit.
        $contenidoPrueba = $json;

        ob_start();

        try {
            require __DIR__ . '/../api/v1/invitaciones/index.php';
        } catch (RespuestaApiTerminada $e) {
            // El endpoint terminó de generar la respuesta HTTP.
        }

        $salida = ob_get_clean();

        return [
            'status' => http_response_code(),
            'json' => json_decode($salida, true),
        ];
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
    public function testRechazaUsuarioSinPermisos(): void
    {
        $respuesta = $this->ejecutarSolicitud('POST', 'alumno');

        $this->assertSame(403, $respuesta['status']);
        $this->assertSame(
            'Acceso denegado',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaIdempotencyKeyVacia(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            ''
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Idempotency-Key requerido',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaIdempotencyKeyMuyLarga(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            str_repeat('a', 101)
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Idempotency-Key inválido',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaJsonInvalido(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            'prueba-001',
            '{json incorrecto'
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'JSON inválido',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaDatosIncompletos(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            'prueba-001',
            '{"email":"alumno@ejemplo.cl"}'
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Datos incompletos',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaEmailInvalido(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            'prueba-001',
            json_encode([
                'email' => 'correo-invalido',
                'rut' => '12345678-9',
                'username' => 'alumno1',
                'rol' => 'alumno'
            ])
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Email inválido',
            $respuesta['json']['title']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaRolInvalido(): void
    {
        $respuesta = $this->ejecutarSolicitud(
            'POST',
            'profesor',
            'prueba-001',
            json_encode([
                'email' => 'alumno@ejemplo.cl',
                'rut' => '12345678-9',
                'username' => 'alumno1',
                'rol' => 'administrador'
            ])
        );

        $this->assertSame(400, $respuesta['status']);
        $this->assertSame(
            'Rol inválido',
            $respuesta['json']['title']
        );
    }
}
