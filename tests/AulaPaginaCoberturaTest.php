<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class AulaPaginaCoberturaTest extends TestCase
{
    private function prepararBaseDeDatos(bool $conAlumnos = true): PDO
    {
        $pdo = new PDO('sqlite::memory:');

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $pdo->exec(
            'CREATE TABLE aulas (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                nombre TEXT,
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
            'CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                rut TEXT,
                username TEXT,
                nombre TEXT,
                apellido TEXT,
                rol TEXT,
                activo INTEGER
            )'
        );

        $pdo->exec(
            'CREATE TABLE aula_alumnos (
                aula_id INTEGER,
                alumno_id INTEGER
            )'
        );

        $pdo->exec(
            "INSERT INTO aulas
                (id, colegio_id, nombre, activo)
             VALUES
                (1, 1, 'Matemática 1A', 1)"
        );

        $pdo->exec(
            'INSERT INTO aula_profesores
                (aula_id, profesor_id)
             VALUES
                (1, 10)'
        );

        if ($conAlumnos) {
            $pdo->exec(
                "INSERT INTO usuarios
                    (id, colegio_id, rut, username,
                     nombre, apellido, rol, activo)
                 VALUES
                    (20, 1, '12345678-9', 'alumno20',
                     'Juan', 'Pérez', 'alumno', 1)"
            );

            $pdo->exec(
                'INSERT INTO aula_alumnos
                    (aula_id, alumno_id)
                 VALUES
                    (1, 20)'
            );
        }

        return $pdo;
    }

    private function ejecutarPagina(bool $conAlumnos): string
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['id'] = '1';
        $archivoDebug = __DIR__ . '/../debug_aula.txt';

file_put_contents($archivoDebug, "INICIO DE PRUEBA\n");

register_shutdown_function(function () use ($archivoDebug): void {
    file_put_contents(
        $archivoDebug,
        "FIN DEL PROCESO\n" .
        "HTTP: " . http_response_code() . "\n" .
        "SESION: " . print_r($_SESSION ?? [], true) . "\n" .
        "ERROR PHP: " . print_r(error_get_last(), true) . "\n" .
        "SALIDA: " . (ob_get_contents() ?: '') . "\n\n",
        FILE_APPEND
    );
});

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Simular un profesor autenticado.
        $_SESSION['usuario_id'] = 10;
        $_SESSION['colegio_id'] = 1;
        $_SESSION['rol'] = 'profesor';

        // Preparar la base de datos aislada.
        $pdo = $this->prepararBaseDeDatos($conAlumnos);

        // Ejecutar la página real.
        ob_start();

        file_put_contents(
    $archivoDebug,
    "ANTES DE AULA.PHP\n",
    FILE_APPEND
); 

        require __DIR__ . '/../aula.php';

        return ob_get_clean();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraAulaConAlumnos(): void
    {
        $html = $this->ejecutarPagina(true);

        $this->assertStringContainsString(
            'Matemática 1A',
            $html
        );

        $this->assertStringContainsString(
            'Juan',
            $html
        );

        $this->assertStringContainsString(
            'alumno20',
            $html
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraAulaSinAlumnos(): void
    {
        $html = $this->ejecutarPagina(false);

        $this->assertStringContainsString(
            'Matemática 1A',
            $html
        );

        $this->assertStringContainsString(
            'No hay alumnos asignados a esta aula.',
            $html
        );
    }
}
