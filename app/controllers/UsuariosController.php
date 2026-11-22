
<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../config/database.php';

class UsuariosController
{
    private $usuario;

    public function __construct()
    {
        $database = new Database();
        $conexion = $database->conectar();

        $this->usuario = new Usuario($conexion);
    }

    // LISTAR USUARIOS
    public function index()
    {
        $this->verificarAdministrador();

        $usuarios = $this->usuario->listar();

        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    // MOSTRAR FORMULARIO DE REGISTRO
   public function crear()
    {
        $this->verificarAdministrador();

        $roles = $this->usuario->listarRoles();

        require_once __DIR__ . '/../views/usuarios/crear.php';
    }

    // GUARDAR USUARIO
    public function guardar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=usuarios");
            exit;
        }

        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'id_rol' => (int) ($_POST['id_rol'] ?? 0)
        ];

        if (
            $datos['nombre_completo'] === '' ||
            $datos['nombre_usuario'] === '' ||
            $datos['password'] === '' ||
            $datos['id_rol'] <= 0
        ) {
            $_SESSION['error'] = "Completa todos los campos obligatorios.";
            header("Location: index.php?route=usuarios-crear");
            exit;
        }

        try {
            $this->usuario->crear($datos);

            $_SESSION['exito'] = "Usuario registrado correctamente.";
            header("Location: index.php?route=usuarios");
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo registrar. Verifica que el usuario o correo no estén repetidos.";
            header("Location: index.php?route=usuarios-crear");
            exit;
        }
    }


    // MOSTRAR FORMULARIO DE EDICION
    public function editar()
    {
        $this->verificarAdministrador();

        $id = (int) ($_GET['id'] ?? 0);

        $usuario = $this->usuario->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error'] = "El usuario no existe.";
            header("Location: index.php?route=usuarios");
            exit;
        }

        $roles = $this->usuario->listarRoles();

        require_once __DIR__ . '/../views/usuarios/editar.php';
    }

    // ACTUALIZAR USUARIO
    public function actualizar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=usuarios");
            exit;
        }

        $id = (int) ($_POST['id_usuario'] ?? 0);

        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'id_rol' => (int) ($_POST['id_rol'] ?? 0)
        ];

        if (
            $id <= 0 ||
            $datos['nombre_completo'] === '' ||
            $datos['nombre_usuario'] === '' ||
            $datos['id_rol'] <= 0
        ) {
            $_SESSION['error'] = "Verifica los campos obligatorios.";
            header("Location: index.php?route=usuarios");
            exit;
        }

        if ($datos['password'] !== '' && strlen($datos['password']) < 8) {
            $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
            header("Location: index.php?route=usuarios-editar&id=" . $id);
            exit;
        }

        try {
            $actualizado = $this->usuario->actualizar($id, $datos);

            if (!$actualizado) {
                throw new Exception("No se pudo actualizar el usuario.");
            }

            $_SESSION['exito'] = "Usuario actualizado correctamente.";
            header("Location: index.php?route=usuarios");
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo actualizar. Verifica que el usuario o correo no estén repetidos.";
            header("Location: index.php?route=usuarios-editar&id=" . $id);
            exit;
        }
    }

    // ACTIVAR O DESACTIVAR USUARIO
    public function cambiarEstado()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=usuarios");
            exit;
        }

        $id = (int) ($_POST['id_usuario'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        if ($id <= 0 || !in_array($estado, ['Activo', 'Inactivo'], true)) {
            $_SESSION['error'] = "Solicitud no válida.";
            header("Location: index.php?route=usuarios");
            exit;
        }

        // Evitar que el administrador desactive su propia cuenta.
        if ($id === (int) $_SESSION['usuario']['id_usuario'] && $estado === 'Inactivo') {
            $_SESSION['error'] = "No puedes desactivar tu propia cuenta.";
            header("Location: index.php?route=usuarios");
            exit;
        }

        try {
            $this->usuario->cambiarEstado($id, $estado);

            $_SESSION['exito'] = "Estado del usuario actualizado correctamente.";

        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo cambiar el estado del usuario.";
        }

        header("Location: index.php?route=usuarios");
        exit;
    }


    // VERIFICAR ADMINISTRADOR
    private function verificarAdministrador()
    {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?route=login");
            exit;
        }

        if ((int) $_SESSION['usuario']['id_rol'] !== 1) {
            http_response_code(403);
            exit("No tienes permiso para acceder a este módulo.");
        }
    }

    // VERIFICAR TOKEN CSRF
    private function verificarToken()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            $_SESSION['error'] = "La solicitud no es válida.";
            header("Location: index.php?route=usuarios");
            exit;
        }
    }
}
