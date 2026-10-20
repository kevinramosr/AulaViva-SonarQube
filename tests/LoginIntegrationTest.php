
<?php

use PHPUnit\Framework\TestCase;

final class LoginIntegrationTest extends TestCase
{
    public function testBaseDeDatosTemporalPermiteConsultarUsuario(): void
    {
        // Base de datos temporal, independiente de Supabase.
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tabla mínima necesaria para comprobar la consulta del login.
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

        $hash = password_hash('ClaveDePrueba123', PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios
             (id, colegio_id, rut, username, nombre, apellido,
              password_hash, rol, activo)
             VALUES
             (1, 10, :rut, :username, :nombre, :apellido,
              :password_hash, :rol, 1)'
        );

        $stmt->execute([
            ':rut' => '11111111-1',
            ':username' => 'usuario_prueba',
            ':nombre' => 'Usuario',
            ':apellido' => 'Prueba',
            ':password_hash' => $hash,
            ':rol' => 'profesor'
        ]);

        // Misma consulta SQL utilizada en el endpoint.
        $consulta = $pdo->prepare(
            'SELECT id, colegio_id, rut, username, nombre,
                    apellido, password_hash, rol, activo
             FROM usuarios
             WHERE rut = :rut OR username = :username
             LIMIT 1'
        );

        $consulta->execute([
            ':rut' => 'usuario_prueba',
            ':username' => 'usuario_prueba'
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        $this->assertIsArray($usuario);
        $this->assertSame('usuario_prueba', $usuario['username']);
        $this->assertTrue(
            password_verify('ClaveDePrueba123', $usuario['password_hash'])
        );
    }
}
