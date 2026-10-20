<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class LoginCoberturaTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testMuestraFormularioDeLogin(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Base de datos aislada: no usa Supabase.
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        ob_start();
        require __DIR__ . '/../login.php';
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'Iniciar sesi',
            $html
        );

        $this->assertStringContainsString(
            'name="identificador"',
            $html
        );

        $this->assertStringContainsString(
            'name="password"',
            $html
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaCamposVacios(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'identificador' => '',
            'password' => ''
        ];

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        ob_start();
        require __DIR__ . '/../login.php';
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'Debes completar todos los campos.',
            $html
        );
    }
    
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaUsuarioInexistente(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'identificador' => 'usuario_inexistente',
            'password' => 'clave123'
        ];

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec(
            'CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                rut TEXT,
                username TEXT,
                nombre TEXT,
                apellido TEXT,
                password_hash TEXT,
                rol TEXT,
                activo INTEGER
            )'
        );

        ob_start();
        require __DIR__ . '/../login.php';
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'incorrectos',
            $html
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRechazaContrasenaIncorrecta(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'identificador' => 'profesor1',
            'password' => 'clave_incorrecta'
        ];

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec(
            'CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY,
                colegio_id INTEGER,
                rut TEXT,
                username TEXT,
                nombre TEXT,
                apellido TEXT,
                password_hash TEXT,
                rol TEXT,
                activo INTEGER
            )'
        );

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios
                (id, colegio_id, rut, username, nombre,
                 apellido, password_hash, rol, activo)
             VALUES
                (1, 1, :rut, :username, :nombre,
                 :apellido, :password_hash, :rol, 1)'
        );

        $stmt->execute([
            ':rut' => '12345678-9',
            ':username' => 'profesor1',
            ':nombre' => 'Profesor',
            ':apellido' => 'Prueba',
            ':password_hash' => password_hash('clave_correcta', PASSWORD_DEFAULT),
            ':rol' => 'profesor'
        ]);

        ob_start();
        require __DIR__ . '/../login.php';
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'incorrectos',
            $html
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testManejaErrorDeBaseDeDatos(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'identificador' => 'profesor1',
            'password' => 'clave123'
        ];

        // SQLite aislado, sin tabla usuarios.
        // La consulta producirá una PDOException.
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        ob_start();
        require __DIR__ . '/../login.php';
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'No se pudo iniciar sesi',
            $html
        );
    }

}
