
<?php

class Categoria
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // LISTAR CATEGORIAS
    public function listar()
    {
        $sql = "SELECT * FROM categorias
                ORDER BY id_categoria ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // REGISTRAR CATEGORIA
    public function crear($datos)
    {
        $sql = "INSERT INTO categorias
                (nombre_categoria, descripcion, estado)
                VALUES (:nombre, :descripcion, 'Activa')";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $datos['nombre_categoria'],
            ':descripcion' => $datos['descripcion']
        ]);
    }

    // BUSCAR CATEGORIA
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM categorias
                WHERE id_categoria = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    // ACTUALIZAR CATEGORIA
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE categorias SET
                nombre_categoria = :nombre,
                descripcion = :descripcion
                WHERE id_categoria = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':nombre' => $datos['nombre_categoria'],
            ':descripcion' => $datos['descripcion'],
            ':id' => $id
        ]);
    }

    // CAMBIAR ESTADO
    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE categorias
                SET estado = :estado
                WHERE id_categoria = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}
