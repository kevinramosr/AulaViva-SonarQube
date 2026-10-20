<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/funciones_rol.php';

final class RolTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testProfesorPuedeAcceder(): void
    {
        $_SESSION['rol'] = 'profesor';

        exigirRol('profesor');

        $this->assertSame('profesor', $_SESSION['rol']);
    }

    public function testAlumnoPuedeAcceder(): void
    {
        $_SESSION['rol'] = 'alumno';

        exigirRol('alumno');

        $this->assertSame('alumno', $_SESSION['rol']);
    }

    public function testAdministradorPuedeAcceder(): void
    {
        $_SESSION['rol'] = 'admin';

        exigirRol('admin');

        $this->assertSame('admin', $_SESSION['rol']);
    }
    public function testVerificarPermisoProfesor(): void
{
    $resultado = verificarPermisoRol('profesor', 'profesor');

    $this->assertNull($resultado);
}

public function testVerificarPermisoAlumno(): void
{
    $resultado = verificarPermisoRol('alumno', 'alumno');

    $this->assertNull($resultado);
}

public function testVerificarPermisoDenegado(): void
{
    $resultado = verificarPermisoRol('profesor', 'alumno');

    $this->assertSame(403, $resultado);
}

public function testVerificarRolInvalido(): void
{
    $resultado = verificarPermisoRol('superusuario', 'profesor');

    $this->assertSame(500, $resultado);
}
}