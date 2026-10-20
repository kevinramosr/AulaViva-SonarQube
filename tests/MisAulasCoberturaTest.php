<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class MisAulasCoberturaTest extends TestCase
{
    private function crearBaseDeDatos(): PDO
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE aulas (
                id INTEGER PRIMARY KEY,
                nombre TEXT,
                colegio_id INTEGER,
                activo INTEGER
            )'
        );

        $pdo->exec(
            'CREATE TABLE aula_profesores (
                aula_id INTEGER,
                profesor_id INTEGER
            )'
        );

        $pdo->exec(
            "INSERT INTO aulas
                (id, nombre, colegio_id, activo)
             VALUES
                (1, 'Matemáticas', 1, 1),
                (2, 'Lenguaje', 1, 1),
                (3, 'Aula de otro colegio', 2, 1),
                (4, 'Aula inactiva', 1, 0),
                (5, 'Aula de otro profesor', 1, 1),
                (6, '<script>alert(1)</script>', 1, 1)"
        );

        $pdo->exec(
            'INSERT INTO aula_profesores
                (aula_id, profesor_id)
             VALUES
                (1, 10),
                (2, 10),
                (3, 10),
                (4, 10),
                (5, 20),
                (6, 10)'
        );

        return $pdo;
    }

    private function ejecutarPagina(
        int $profesorId = 10,
        int $colegioId = 1,
        bool $provocarError = false
    ): array {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [
            'usuario_id' => $profesorId,
            'colegio_id' => $colegioId,
            'rol' => 'profesor',
            'nombre' => 'Juan Pérez'
        ];

        $pdo = $provocarError
            ? new PDO('sqlite::memory:')
            : $this->crearBaseDeDatos();

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        ob_start();

        require __DIR__ . '/../mis_aulas.php';

        $html = ob_get_clean();

        return [
            'status' => http_response_code(),
            'html' => $html
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraAulasDelProfesor(): void
    {
        $resultado = $this->ejecutarPagina();

        $this->assertContains(
            $resultado['status'],
            [false, 200]
        );

        $this->assertStringContainsString(
            'Matemáticas',
            $resultado['html']
        );

        $this->assertStringContainsString(
            'Lenguaje',
            $resultado['html']
        );

        $this->assertStringContainsString(
            'aula.php?id=1',
            $resultado['html']
        );

        $this->assertStringContainsString(
            'aula.php?id=2',
            $resultado['html']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testNoMuestraAulasDeOtros(): void
    {
        $resultado = $this->ejecutarPagina();

        $this->assertStringNotContainsString(
            'Aula de otro colegio',
            $resultado['html']
        );

        $this->assertStringNotContainsString(
            'Aula de otro profesor',
            $resultado['html']
        );

        $this->assertStringNotContainsString(
            'Aula inactiva',
            $resultado['html']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraMensajeSinAulas(): void
    {
        $resultado = $this->ejecutarPagina(999);

        $this->assertStringContainsString(
            'No tienes aulas asignadas.',
            $resultado['html']
        );

        $this->assertStringNotContainsString(
            'aula.php?id=',
            $resultado['html']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testEscapaHtmlEnNombreDeAula(): void
    {
        $resultado = $this->ejecutarPagina();

        $this->assertStringContainsString(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            $resultado['html']
        );

        $this->assertStringNotContainsString(
            '<script>alert(1)</script>',
            $resultado['html']
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraNombreDelProfesor(): void
    {
        $resultado = $this->ejecutarPagina();

        $this->assertStringContainsString(
            'Juan Pérez',
            $resultado['html']
        );

        $this->assertStringContainsString(
            'Cerrar sesión',
            $resultado['html']
        );
    }
}
