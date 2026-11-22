
<?php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../../config/database.php';

class ProductosController
{
    private $producto;

    public function __construct()
    {
        $database = new Database();
        $conexion = $database->conectar();

        $this->producto = new Producto($conexion);
    }

    // LISTAR PRODUCTOS
    public function index()
    {
        $this->verificarAdministrador();

        $productos = $this->producto->listar();

        require_once __DIR__ . '/../views/productos/index.php';
    }

    // MOSTRAR FORMULARIO DE REGISTRO
    public function crear()
    {
        $this->verificarAdministrador();

        $categorias = $this->producto->listarCategorias();

        require_once __DIR__ . '/../views/productos/crear.php';
    }

    // GUARDAR PRODUCTO
    public function guardar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=productos");
            exit;
        }

        $nombre = trim($_POST['nombre_producto'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $precio = $_POST['precio'] ?? '';
        $disponible = $_POST['disponible'] ?? '';

        if (
            $nombre === '' ||
            !$categoria ||
            !is_numeric($precio) ||
            (float)$precio < 0 ||
            !in_array((string)$disponible, ['0', '1'], true)
        ) {
            $_SESSION['error'] = "Verifica los datos del producto.";
            header("Location: index.php?route=productos-crear");
            exit;
        }

        try {
            $this->producto->crear([
                'id_categoria' => $categoria,
                'nombre_producto' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'disponible' => $disponible
            ]);

            $_SESSION['mensaje'] = "Producto registrado correctamente.";

        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo registrar el producto.";
        }

        header("Location: index.php?route=productos");
        exit;
    }

    // MOSTRAR FORMULARIO DE EDICION
    public function editar()
    {
        $this->verificarAdministrador();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header("Location: index.php?route=productos");
            exit;
        }

        $producto = $this->producto->obtenerPorId($id);

        if (!$producto) {
            $_SESSION['error'] = "El producto no existe.";
            header("Location: index.php?route=productos");
            exit;
        }

        $categorias = $this->producto->listarCategorias();

        require_once __DIR__ . '/../views/productos/editar.php';
    }

    // ACTUALIZAR PRODUCTO
    public function actualizar()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=productos");
            exit;
        }

        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $categoria = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre_producto'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $disponible = $_POST['disponible'] ?? '';

        if (
            !$id ||
            !$categoria ||
            $nombre === '' ||
            !is_numeric($precio) ||
            (float)$precio < 0 ||
            !in_array((string)$disponible, ['0', '1'], true)
        ) {
            $_SESSION['error'] = "Verifica los datos del producto.";
            header("Location: index.php?route=productos");
            exit;
        }

        try {
            $this->producto->actualizar($id, [
                'id_categoria' => $categoria,
                'nombre_producto' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'disponible' => $disponible
            ]);

            $_SESSION['mensaje'] = "Producto actualizado correctamente.";

        } catch (PDOException $e) {
            $_SESSION['error'] = "No se pudo actualizar el producto.";
        }

        header("Location: index.php?route=productos");
        exit;
    }

    // ACTIVAR O DESACTIVAR PRODUCTO
    public function cambiarEstado()
    {
        $this->verificarAdministrador();
        $this->verificarToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=productos");
            exit;
        }

        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $estado = $_POST['estado'] ?? '';

        if (
            !$id ||
            !in_array($estado, ['Activo', 'Inactivo'], true)
        ) {
            $_SESSION['error'] = "Datos de estado no válidos.";
            header("Location: index.php?route=productos");
            exit;
        }

        $this->producto->cambiarEstado($id, $estado);

        $_SESSION['mensaje'] = "Estado del producto actualizado.";

        header("Location: index.php?route=productos");
        exit;
    }

    // VERIFICAR ADMINISTRADOR
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
