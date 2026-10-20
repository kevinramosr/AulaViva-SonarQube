<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AulasDetalleCoberturaTest extends TestCase
{
    private function ejecutarEndpoint(string $endpoint, string $id): array
    {
        $script = __DIR__ . '/fixtures/aulas_endpoint.php';

        $comando = escapeshellarg(PHP_BINARY)
            . ' '
            . escapeshellarg($script)
            . ' '
            . escapeshellarg($endpoint)
            . ' '
            . escapeshellarg($id);

        exec($comando . ' 2>&1', $salida, $codigo);

        $this->assertSame(
            0,
            $codigo,
            "El proceso terminó con error:\n" . implode("\n", $salida)
        );

        $respuesta = json_decode(implode("\n", $salida), true);

        $this->assertIsArray($respuesta);

        return $respuesta;
    }

    public function testDetalleDevuelveAulaAutorizada(): void
    {
        $respuesta = $this->ejecutarEndpoint('detalle', '1');

        $this->assertSame(200, $respuesta['_http_status']);
        $this->assertSame(1, $respuesta['data']['id']);
        $this->assertSame('Matemáticas', $respuesta['data']['nombre']);
    }

    public function testAlumnosDevuelveSoloAlumnosActivos(): void
    {
        $respuesta = $this->ejecutarEndpoint('alumnos', '1');

        $this->assertSame(200, $respuesta['_http_status']);
        $this->assertCount(2, $respuesta['data']);
        $this->assertSame(
            ['Alonso', 'Zúñiga'],
            array_column($respuesta['data'], 'apellido')
        );
    }
}
