
<?php

class Producto
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // LISTAR PRODUCTOS CON SU CATEGORIA
    public function listar()
    {
        $sql = "SELECT
                    p.id_producto,
                    p.id_categoria,
                    p.nombre_producto,
                    p.descripcion,
                    p.precio,
                    p.disponible,
                    p.estado,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categorias c
                    ON p.id_categoria = c.id_categoria
                ORDER BY p.id_producto DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // LISTAR CATEGORIAS ACTIVAS
    public function listarCategorias()
    {
        $sql = "SELECT id_categoria, nombre_categoria
                FROM categorias
                WHERE estado = 'Activa'
                ORDER BY nombre_categoria ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // REGISTRAR PRODUCTO
    public function crear($datos)
    {
        $sql = "INSERT INTO productos
                (
                    id_categoria,
                    nombre_producto,
                    descripcion,
                    precio,
                    disponible,
                    estado
                )
                VALUES
                (
                    :categoria,
                    :nombre,
                    :descripcion,
                    :precio,
                    :disponible,
                    'Activo'
                )";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':categoria' => $datos['id_categoria'],
            ':nombre' => $datos['nombre_producto'],
            ':descripcion' => $datos['descripcion'],
            ':precio' => $datos['precio'],
            ':disponible' => $datos['disponible']
        ]);
    }

    // BUSCAR PRODUCTO POR ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT *
                FROM productos
                WHERE id_producto = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    // ACTUALIZAR PRODUCTO
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE productos SET
                    id_categoria = :categoria,
                    nombre_producto = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    disponible = :disponible
                WHERE id_producto = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':categoria' => $datos['id_categoria'],
            ':nombre' => $datos['nombre_producto'],
            ':descripcion' => $datos['descripcion'],
            ':precio' => $datos['precio'],
            ':disponible' => $datos['disponible'],
            ':id' => $id
        ]);
    }

    // CAMBIAR ESTADO
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE productos
                SET estado = :estado
                WHERE id_producto = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}
