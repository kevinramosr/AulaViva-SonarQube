<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/v1/_helpers/api.php';

final class PermisoApiTest extends TestCase
{
    public function testProfesorAutorizado(): void
    {
        $sesion = [
            'usuario_id' => 1,
            'rol' => 'profesor'
        ];

        $this->assertNull(
            verificarPermisoApi('profesor', $sesion)
        );
    }

    public function testAlumnoAutorizado(): void
    {
        $sesion = [
            'usuario_id' => 2,
            'rol' => 'alumno'
        ];

        $this->assertNull(
            verificarPermisoApi('alumno', $sesion)
        );
    }

    public function testUsuarioNoAutenticado(): void
    {
        $this->assertSame(
            401,
            verificarPermisoApi('profesor', [])
        );
    }

    public function testSesionSinRol(): void
    {
        $sesion = [
            'usuario_id' => 1
        ];

        $this->assertSame(
            401,
            verificarPermisoApi('profesor', $sesion)
        );
    }

    public function testUsuarioSinPermisos(): void
    {
        $sesion = [
            'usuario_id' => 2,
            'rol' => 'alumno'
        ];

        $this->assertSame(
            403,
            verificarPermisoApi('profesor', $sesion)
        );
    }
}