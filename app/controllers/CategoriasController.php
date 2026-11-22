
<?php

require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../../config/database.php';

class CategoriasController
{
    private $categoria;

    public function __construct()
    {
        $database = new Database();
        $conexion = $database->conectar();

        $this->categoria = new Categoria($conexion);
    }

    // LISTAR CATEGORIAS
    public function index()
    {
        $this->verificarAdministrador();

        $categorias = $this->categoria->listar();

        require_once __DIR__ . '/../views/categorias/index.php';
    }

    // MOSTRAR FORMULARIO DE REGISTRO
    public function crear()
    {
        $this->verificarAdministrador();

        require_once __DIR__ . '/../views/categorias/crear.php';
    }

    // GUARDAR CATEGORIA
    public function guardar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=categorias");
            exit;
        }

        $nombre = trim($_POST['nombre_categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $_SESSION['error'] = "El nombre de la categoría es obligatorio.";
            header("Location: index.php?route=categorias-crear");
            exit;
        }

        try {
            $this->categoria->crear([
                'nombre_categoria' => $nombre,
                'descripcion' => $descripcion
            ]);

            $_SESSION['mensaje'] = "Categoría registrada correctamente.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo registrar la categoría. Verifica los datos.";
        }

        header("Location: index.php?route=categorias");
        exit;
    }

    // MOSTRAR FORMULARIO DE EDICION
    public function editar()
    {
        $this->verificarAdministrador();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header("Location: index.php?route=categorias");
            exit;
        }

        $categoria = $this->categoria->obtenerPorId($id);

        if (!$categoria) {
            $_SESSION['error'] = "La categoría no existe.";
            header("Location: index.php?route=categorias");
            exit;
        }

        require_once __DIR__ . '/../views/categorias/editar.php';
    }

    // ACTUALIZAR CATEGORIA
    public function actualizar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=categorias");
            exit;
        }

        $id = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre_categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (!$id || $nombre === '') {
            $_SESSION['error'] = "Verifica los datos de la categoría.";
            header("Location: index.php?route=categorias");
            exit;
        }

        try {
            $resultado = $this->categoria->actualizar($id, [
                'nombre_categoria' => $nombre,
                'descripcion' => $descripcion
            ]);

            $_SESSION['mensaje'] = $resultado
                ? "Categoría actualizada correctamente."
                : "No se realizaron cambios.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo actualizar la categoría.";
        }

        header("Location: index.php?route=categorias");
        exit;
    }

    // ACTIVAR O DESACTIVAR CATEGORIA
    public function cambiarEstado()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=categorias");
            exit;
        }

        $id = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $estado = $_POST['estado'] ?? '';

        if (!$id || !in_array($estado, ['Activa', 'Inactiva'], true)) {
            $_SESSION['error'] = "Datos de estado no válidos.";
            header("Location: index.php?route=categorias");
            exit;
        }

        $this->categoria->cambiarEstado($id, $estado);

        $_SESSION['mensaje'] = "Estado de la categoría actualizado.";

        header("Location: index.php?route=categorias");
        exit;
    }

    // VERIFICAR QUE SEA ADMINISTRADOR
    private function verificarAdministrador()
    {
        if (
            !isset($_SESSION['usuario']) ||
            (int) $_SESSION['usuario']['id_rol'] !== 1
        ) {
            header("Location: index.php?route=dashboard");
            exit;
        }
    }

    // VERIFICAR TOKEN CSRF
    private function verificarToken()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (
            !isset($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            die("Solicitud no válida. Actualiza la página e intenta nuevamente.");
        }
    }
}
