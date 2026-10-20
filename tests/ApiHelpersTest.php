<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/v1/_helpers/api.php';

final class ApiHelpersTest extends TestCase
{
    public function testProfesorPuedeAcceder(): void
    {
        $sesion = [
            'usuario_id' => 1,
            'rol' => 'profesor'
        ];

        $resultado = verificarPermisoApi('profesor', $sesion);

        $this->assertNull($resultado);
    }

    public function testAlumnoPuedeAcceder(): void
    {
        $sesion = [
            'usuario_id' => 2,
            'rol' => 'alumno'
        ];

        $resultado = verificarPermisoApi('alumno', $sesion);

        $this->assertNull($resultado);
    }

    public function testUsuarioNoAutenticado(): void
    {
        $resultado = verificarPermisoApi('profesor', []);

        $this->assertSame(401, $resultado);
    }

    public function testUsuarioSinPermisos(): void
    {
        $sesion = [
            'usuario_id' => 3,
            'rol' => 'alumno'
        ];

        $resultado = verificarPermisoApi('profesor', $sesion);

        $this->assertSame(403, $resultado);
    }
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
#[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testExigirRolApiPermiteAccesoAlProfesor(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [
            'usuario_id' => 1,
            'rol' => 'profesor'
        ];

        exigirRolApi('profesor');

        $this->assertSame('profesor', $_SESSION['rol']);
        $this->assertSame(1, $_SESSION['usuario_id']);
    }

}
