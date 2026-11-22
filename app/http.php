<?php
declare(strict_types=1);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $route): never
{
    header('Location: index.php?route=' . rawurlencode($route), true, 303);
    exit;
}

function csrfToken(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function validarCsrf(): void
{
    $token = $_POST['csrf_token'] ?? null;
    if (!isset($_SESSION['csrf_token']) || !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        responderError(403, 'Solicitud no válida. Vuelve a cargar la página.');
    }
}

function responderError(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
}

function terminarSesion(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $cookie['path'],
        'domain' => $cookie['domain'],
        'secure' => $cookie['secure'],
        'httponly' => $cookie['httponly'],
        'samesite' => $cookie['samesite'],
    ]);
    session_destroy();
}

function usuarios(): Usuario
{
    static $model;
    return $model ??= new Usuario((new Database())->conectar());
}

function usuarioActual(): array
{
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        redirect('login');
    }

    $usuario = usuarios()->buscarActivo((int) $_SESSION['usuario']['id_usuario']);
    if (!$usuario) {
        terminarSesion();
        redirect('login');
    }

    $_SESSION['actividad'] = time();
    return $usuario;
}

