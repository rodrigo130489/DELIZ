<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');

require __DIR__ . '/../app/http.php';
require __DIR__ . '/../app/controllers/LoginController.php';
require __DIR__ . '/../app/models/Usuario.php';
require __DIR__ . '/../config/database.php';

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");

session_name('DELIZSESSID');
session_set_cookie_params([
    'path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/',
    'httponly' => true,
    'secure' => getenv('DELIZ_SECURE_COOKIE') === '1' ||
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax',
]);

try {
    if (!session_start()) {
        throw new RuntimeException('No se pudo iniciar la sesión.');
    }
    if (isset($_SESSION['usuario']) &&
        (time() - ($_SESSION['actividad'] ?? 0) >= 1800 ||
         time() - ($_SESSION['inicio'] ?? 0) >= 28800)) {
        terminarSesion();
        redirect('login');
    }

    $routes = [
        'login' => ['GET', null],
        'autenticar' => ['POST', null],
        'logout' => ['POST', null],
        'dashboard' => ['GET', []],
        'usuarios' => ['GET', [1]],
    ];

    $route = $_GET['route'] ?? 'login';
    if (!is_string($route) || !isset($routes[$route])) {
        responderError(404, 'Página no encontrada.');
    }

    [$method, $roles] = $routes[$route];
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        header('Allow: ' . $method);
        responderError(405, 'Método no permitido.');
    }

    if ($method === 'POST') {
        validarCsrf();
    }

    if ($roles !== null) {
        $usuario = usuarioActual();
        if ($roles !== [] && !in_array((int) $usuario['id_rol'], $roles, true)) {
            responderError(403, 'No tienes permiso para acceder a este recurso.');
        }
    }

    $login = new LoginController();
    switch ($route) {
        case 'login':
            $login->index();
            break;
        case 'autenticar':
            $login->autenticar();
            break;
        case 'logout':
            $login->cerrarSesion();
            break;
        case 'dashboard':
            require __DIR__ . '/../app/views/dashboard/dashboard.php';
            break;
        case 'usuarios':
            $lista = usuarios()->listar();
            require __DIR__ . '/../app/views/usuarios/index.php';
            break;
    }
} catch (PDOException $error) {
    error_log('DELIZ: fallo de base de datos (' . $error->getCode() . ')');
    responderError(503, 'El servicio de datos no está disponible.');
} catch (Throwable $error) {
    error_log('DELIZ: ' . get_class($error) . ' en ' . basename($error->getFile()) . ':' . $error->getLine());
    responderError(500, 'Ocurrió un error interno.');
}

