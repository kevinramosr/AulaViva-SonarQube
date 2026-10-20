<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/v1/_helpers/api.php';

final class ConstruirProblemaTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = '/Aula%20viva/api/v1/aulas/';
    }

    public function testConstruirError401(): void
    {
        $resultado = construirProblema(
            401,
            'No autenticado',
            'Debes iniciar sesión.',
            '/errors/no-autenticado'
        );

        $this->assertSame(401, $resultado['status']);
        $this->assertSame('No autenticado', $resultado['title']);
        $this->assertSame('/errors/no-autenticado', $resultado['type']);
    }

    public function testConstruirError403(): void
    {
        $resultado = construirProblema(
            403,
            'Acceso denegado',
            'No tienes permisos.',
            '/errors/acceso-denegado'
        );

        $this->assertSame(403, $resultado['status']);
        $this->assertSame('Acceso denegado', $resultado['title']);
        $this->assertSame('No tienes permisos.', $resultado['detail']);
    }

    public function testTipoDeErrorPredeterminado(): void
    {
        $resultado = construirProblema(
            500,
            'Error interno',
            'Ocurrió un error.'
        );

        $this->assertSame('about:blank', $resultado['type']);
        $this->assertSame(500, $resultado['status']);
    }

    public function testIncluyeRutaDeSolicitud(): void
    {
        $resultado = construirProblema(
            404,
            'No encontrado',
            'El recurso no existe.'
        );

        $this->assertSame(
            '/Aula%20viva/api/v1/aulas/',
            $resultado['instance']
        );
    }
}