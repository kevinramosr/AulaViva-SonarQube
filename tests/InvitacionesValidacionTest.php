<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/v1/invitaciones/validacion.php';

final class InvitacionesValidacionTest extends TestCase
{
    private static function datosValidos(): array
    {
        return [
            'email' => 'alumno@ejemplo.cl',
            'rut' => '12345678-9',
            'username' => 'alumno123',
            'rol' => 'alumno',
        ];
    }

    public function testInvitacionValida(): void
    {
        $this->assertNull(
            validarDatosInvitacion(self::datosValidos())
        );
    }

    public static function camposObligatorios(): array
    {
        return [
            'sin email' => ['email'],
            'sin rut' => ['rut'],
            'sin username' => ['username'],
            'sin rol' => ['rol'],
        ];
    }

    #[DataProvider('camposObligatorios')]
    public function testCampoObligatorio(string $campo): void
    {
        $datos = self::datosValidos();
        unset($datos[$campo]);

        $error = validarDatosInvitacion($datos);

        $this->assertIsArray($error);
        $this->assertSame(400, $error['status']);
        $this->assertSame(
            '/errors/datos-incompletos',
            $error['type']
        );
    }

    public function testEmailInvalido(): void
    {
        $datos = self::datosValidos();
        $datos['email'] = 'correo-invalido';

        $error = validarDatosInvitacion($datos);

        $this->assertIsArray($error);
        $this->assertSame(
            '/errors/email-invalido',
            $error['type']
        );
    }

    public function testRolInvalido(): void
    {
        $datos = self::datosValidos();
        $datos['rol'] = 'administrador';

        $error = validarDatosInvitacion($datos);

        $this->assertIsArray($error);
        $this->assertSame(
            '/errors/rol-invalido',
            $error['type']
        );
    }

    public function testRolProfesorEsValido(): void
    {
        $datos = self::datosValidos();
        $datos['rol'] = 'profesor';

        $this->assertNull(
            validarDatosInvitacion($datos)
        );
    }

    public function testSeEliminanEspaciosEnValidacion(): void
    {
        $datos = self::datosValidos();
        $datos['email'] = '  alumno@ejemplo.cl  ';
        $datos['rol'] = ' alumno ';

        $this->assertNull(
            validarDatosInvitacion($datos)
        );
    }
}
