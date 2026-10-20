<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApiErroresTest extends TestCase
{
    public function testUsuarioSinSesionRecibe401(): void
    {
        $resultado = $this->ejecutarCaso('sin-sesion');

        $this->assertSame(401, $resultado['status']);
        $this->assertSame('No autenticado', $resultado['title']);
        $this->assertSame('/errors/no-autenticado', $resultado['type']);
    }

    public function testUsuarioConRolIncorrectoRecibe403(): void
    {
        $resultado = $this->ejecutarCaso('rol-incorrecto');

        $this->assertSame(403, $resultado['status']);
        $this->assertSame('Acceso denegado', $resultado['title']);
        $this->assertSame('/errors/acceso-denegado', $resultado['type']);
    }

    
    private function ejecutarCaso(string $caso): array
    {
        $comando = [
            PHP_BINARY,
            __DIR__ . '/fixtures/probar_error_api.php',
            $caso
        ];

        $proceso = proc_open(
            $comando,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ],
            $pipes
        );

        $this->assertIsResource($proceso);

        fclose($pipes[0]);

        $salida = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $codigo = proc_close($proceso);

        $this->assertSame('', $error, 'El proceso PHP generó errores.');

        $resultado = json_decode($salida, true);

        $this->assertIsArray(
            $resultado,
            'La API no devolvió un JSON válido.'
        );

        return $resultado;
    }

    
    public function testResponderProblemaDevuelveJSONDeError(): void
    {
        $resultado = $this->ejecutarCaso('responder-problema');

        $this->assertSame(422, $resultado['status']);
        $this->assertSame('Datos inválidos', $resultado['title']);
        $this->assertSame(
            '/errors/datos-invalidos',
            $resultado['type']
        );
    }

}