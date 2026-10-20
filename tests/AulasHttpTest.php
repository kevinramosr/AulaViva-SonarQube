<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AulasHttpTest extends TestCase
{
    private static $servidor = null;
    private static int $puerto = 8766;

    public static function setUpBeforeClass(): void
    {
        $raiz = dirname(__DIR__);

        self::$servidor = proc_open(
            [
                PHP_BINARY,
                '-S',
                '127.0.0.1:' . self::$puerto,
                '-t',
                $raiz,
                $raiz . '/tests/http/aulas_router.php'
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['file', 'NUL', 'a'],
                2 => ['file', 'NUL', 'a']
            ],
            $pipes,
            $raiz
        );

        if (!is_resource(self::$servidor)) {
            throw new RuntimeException('No se pudo iniciar el servidor de pruebas.');
        }

        fclose($pipes[0]);

        $disponible = false;

        for ($i = 0; $i < 30; $i++) {
            $conexion = @fsockopen(
                '127.0.0.1',
                self::$puerto,
                $errno,
                $error,
                0.1
            );

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

            throw new RuntimeException('El servidor de pruebas no respondió.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$servidor)) {
            proc_terminate(self::$servidor);
            proc_close(self::$servidor);
        }
    }

    public function testProfesorObtieneSoloSusAulasActivas(): void
    {
        $contexto = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'timeout' => 5
            ]
        ]);

        $cuerpo = file_get_contents(
            'http://127.0.0.1:' . self::$puerto . '/api/v1/aulas/',
            false,
            $contexto
        );

        $this->assertNotFalse($cuerpo);

        $respuesta = json_decode($cuerpo, true);

        $this->assertIsArray($respuesta);
        $this->assertSame(
            ['Ciencias', 'Matemáticas'],
            array_column($respuesta['data'], 'nombre')
        );
    }

    public function testMetodoPostDevuelve405(): void
    {
        $contexto = stream_context_create([
            'http' => [
                'method' => 'POST',
                'ignore_errors' => true,
                'timeout' => 5
            ]
        ]);

        file_get_contents(
            'http://127.0.0.1:' . self::$puerto . '/api/v1/aulas/',
            false,
            $contexto
        );

        $estado = 0;

        foreach ($http_response_header ?? [] as $cabecera) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $cabecera, $coincidencias)) {
                $estado = (int) $coincidencias[1];
                break;
            }
        }

        $this->assertSame(405, $estado);
    }
}
