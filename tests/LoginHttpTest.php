<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LoginHttpTest extends TestCase
{
    private static $servidor = null;
    private static int $puerto = 8765;

    public static function setUpBeforeClass(): void
    {
        $raiz = dirname(__DIR__);
        $router = $raiz . '/tests/http/login_router.php';

        $comando = [
            PHP_BINARY,
            '-S',
            '127.0.0.1:' . self::$puerto,
            '-t',
            $raiz,
            $router
        ];

        self::$servidor = proc_open(
            $comando,
            [
                0 => ['pipe', 'r'],
                1 => ['file', 'NUL', 'a'],
                2 => ['file', 'NUL', 'a']
            ],
            $pipes,
            $raiz
        );

        if (!is_resource(self::$servidor)) {
            throw new RuntimeException('No se pudo iniciar el servidor PHP.');
        }

        fclose($pipes[0]);

        $disponible = false;

        for ($i = 0; $i < 30; $i++) {
            $conexion = @fsockopen('127.0.0.1', self::$puerto, $errno, $error, 0.1);

            if ($conexion !== false) {
                fclose($conexion);
                $disponible = true;
                break;
            }

            usleep(100000);
        }

        if (!$disponible) {
            proc_terminate(self::$servidor);
            proc_close(self::$servidor);
            self::$servidor = null;
            throw new RuntimeException('El servidor PHP no respondió.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$servidor)) {
            proc_terminate(self::$servidor);
            proc_close(self::$servidor);
        }
    }

    private function solicitar(string $metodo, ?array $datos = null): array
    {
        $url = 'http://127.0.0.1:' . self::$puerto . '/api/v1/auth/login/';

        $opciones = [
            'http' => [
                'method' => $metodo,
                'ignore_errors' => true,
                'header' => "Content-Type: application/json\r\n",
                'timeout' => 5
            ]
        ];

        if ($datos !== null) {
            $opciones['http']['content'] = json_encode($datos);
        }

        $contexto = stream_context_create($opciones);
        $respuesta = file_get_contents($url, false, $contexto);

        $estado = 0;

        foreach ($http_response_header ?? [] as $cabecera) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $cabecera, $coincidencias)) {
                $estado = (int) $coincidencias[1];
                break;
            }
        }

        return [
            'estado' => $estado,
            'datos' => json_decode($respuesta ?: '', true)
        ];
    }

    public function testLoginCorrecto(): void
    {
        $respuesta = $this->solicitar('POST', [
            'identificador' => 'usuario_prueba',
            'password' => 'ClaveDePrueba123'
        ]);

        $this->assertSame(200, $respuesta['estado']);
        $this->assertSame('usuario_prueba', $respuesta['datos']['data']['username']);
    }

    public function testPasswordIncorrecta(): void
    {
        $respuesta = $this->solicitar('POST', [
            'identificador' => 'usuario_prueba',
            'password' => 'ClaveIncorrecta'
        ]);

        $this->assertSame(401, $respuesta['estado']);
    }

    public function testDatosIncompletos(): void
    {
        $respuesta = $this->solicitar('POST', [
            'identificador' => 'usuario_prueba'
        ]);

        $this->assertSame(400, $respuesta['estado']);
    }

    public function testMetodoGetNoPermitido(): void
    {
        $respuesta = $this->solicitar('GET');

        $this->assertSame(405, $respuesta['estado']);
    }
}
