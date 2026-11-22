<?php
declare(strict_types=1);

final class LoginController
{
    public function index(): void
    {
        if (isset($_SESSION['usuario'])) {
            redirect('dashboard');
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function autenticar(): void
    {
        $name = $_POST['nombre_usuario'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!is_string($name) || !is_string($password) ||
            trim($name) === '' || strlen($name) > 200 ||
            $password === '' || strlen($password) > 4096) {
            $_SESSION['error'] = 'Ingresa un usuario y una contraseña válidos.';
            redirect('login');
        }

        $usuario = usuarios()->iniciarSesion(trim($name), $password);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos, o cuenta inactiva.';
            redirect('login');
        }

        if (!session_regenerate_id(true)) {
            throw new RuntimeException('No se pudo renovar la sesión.');
        }

        $_SESSION = [
            'usuario' => ['id_usuario' => (int) $usuario['id_usuario']],
            'inicio' => time(),
            'actividad' => time(),
        ];

        redirect('dashboard');
    }

    public function cerrarSesion(): void
    {
        terminarSesion();
        redirect('login');
    }
}

