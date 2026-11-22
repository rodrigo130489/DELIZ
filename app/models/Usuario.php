
<?php

final class Usuario
{
    private PDO $conexion;


    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Buscar usuario e iniciar sesión
    public function iniciarSesion(string $nombre_usuario, string $password): array|false
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.nombre_completo,
                    u.nombre_usuario,
                    u.correo,
                    u.password,
                    u.id_rol,
                    u.estado,
                    r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                WHERE u.nombre_usuario = :nombre_usuario
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ':nombre_usuario',
            $nombre_usuario
        );

        $consulta->execute();

        $usuario = $consulta->fetch();

        // Verificar que el usuario exista y esté activo
        if (!$usuario || $usuario['estado'] !== 'Activo') {
            return false;
        }

        // Verificar contraseña cifrada
        if (!password_verify($password, $usuario['password'])) {
            return false;
        }

        // No devolver la contraseña
        unset($usuario['password']);

        return $usuario;
    }

    public function buscarActivo(int $id): array|false
    {
        $consulta = $this->conexion->prepare(
            "SELECT u.id_usuario, u.nombre_completo, u.id_rol, r.nombre_rol
             FROM usuarios u INNER JOIN roles r ON r.id_rol = u.id_rol
             WHERE u.id_usuario = :id AND u.estado = 'Activo'"
        );
        $consulta->execute(['id' => $id]);
        return $consulta->fetch();
    }

    // Listar usuarios
    public function listar(): array
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.nombre_completo,
                    u.nombre_usuario,
                    u.correo,
                    u.estado,
                    r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                ORDER BY u.id_usuario DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }
}
