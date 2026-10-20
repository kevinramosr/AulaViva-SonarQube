<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class CrearInvitacionCoberturaTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCreaInvitacionConTokenSeguro(): void
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE invitaciones (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                colegio_id INTEGER,
                email TEXT,
                rut TEXT,
                username TEXT,
                token_hash TEXT,
                rol TEXT,
                expira_en TEXT
            )'
        );

        ob_start();

        require __DIR__ . '/../crear_invitacion.php';

        $html = ob_get_clean();

        $stmt = $pdo->query(
            'SELECT * FROM invitaciones LIMIT 1'
        );

        $invitacion = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertIsArray($invitacion);

        $this->assertSame(
            'alumno2@prueba.cl',
            $invitacion['email']
        );

        $this->assertSame(
            'alumno2',
            $invitacion['username']
        );

        $this->assertSame(
            'alumno',
            $invitacion['rol']
        );

        $this->assertSame(
            1,
            (int) $invitacion['colegio_id']
        );

        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{64}$/',
            $invitacion['token_hash']
        );

        $this->assertStringContainsString(
            'Invitación creada correctamente.',
            $html
        );

        $this->assertStringContainsString(
            'activar_cuenta.php?token=',
            $html
        );

        preg_match(
            '/token=([a-f0-9]{64})/',
            $html,
            $coincidencias
        );

        $this->assertArrayHasKey(1, $coincidencias);

        $token = $coincidencias[1];

        $this->assertSame(
            hash('sha256', $token),
            $invitacion['token_hash']
        );

        $expiracion = strtotime($invitacion['expira_en']);

        $this->assertNotFalse($expiracion);

        $this->assertEqualsWithDelta(
            time() + 86400,
            $expiracion,
            10
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testNoGuardaTokenOriginalEnBaseDeDatos(): void
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE invitaciones (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                colegio_id INTEGER,
                email TEXT,
                rut TEXT,
                username TEXT,
                token_hash TEXT,
                rol TEXT,
                expira_en TEXT
            )'
        );

        ob_start();

        require __DIR__ . '/../crear_invitacion.php';

        $html = ob_get_clean();

        preg_match(
            '/token=([a-f0-9]{64})/',
            $html,
            $coincidencias
        );

        $this->assertArrayHasKey(1, $coincidencias);

        $token = $coincidencias[1];

        $stmt = $pdo->query(
            'SELECT token_hash FROM invitaciones LIMIT 1'
        );

        $hashGuardado = $stmt->fetchColumn();

        $this->assertNotSame($token, $hashGuardado);

        $this->assertSame(
            hash('sha256', $token),
            $hashGuardado
        );
    }
}
