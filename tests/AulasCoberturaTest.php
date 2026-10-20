<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AulasCoberturaTest extends TestCase
{
 #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
#[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]   
public function testEndpointRealDevuelveAulasActivas(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec(
            'CREATE TABLE aulas (
                id INTEGER PRIMARY KEY,
                nombre TEXT NOT NULL,
                colegio_id INTEGER NOT NULL,
                activo INTEGER NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE aula_profesores (
                aula_id INTEGER NOT NULL,
                profesor_id INTEGER NOT NULL
            )'
        );

        $pdo->exec("
            INSERT INTO aulas VALUES
            (1, 'Matemáticas', 10, 1),
            (2, 'Ciencias', 10, 1),
            (3, 'Inactiva', 10, 0),
            (4, 'Otro colegio', 20, 1)
        ");

        $pdo->exec("
            INSERT INTO aula_profesores VALUES
            (1, 1), (2, 1), (3, 1), (4, 1)
        ");

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/aulas/';

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [
            'usuario_id' => 1,
            'colegio_id' => 10,
            'rol' => 'profesor'
        ];

        ob_start();

        try {
            // Ejecuta el endpoint real, no una copia de su consulta.
            require __DIR__ . '/../api/v1/aulas/index.php';

            $salida = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $respuesta = json_decode($salida, true);

        $this->assertIsArray($respuesta);
        $this->assertCount(2, $respuesta['data']);
        $this->assertSame(
            ['Ciencias', 'Matemáticas'],
            array_column($respuesta['data'], 'nombre')
        );
    }
}
