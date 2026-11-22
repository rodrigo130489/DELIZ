
<?php
/** @var array $usuario */
/** @var array $roles */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/usuarios.css">
</head>
<body>

<div class="container py-5">
    <div class="form-container">

        <div class="form-header">
            <div class="brand-icon">D</div>
            <div>
                <h2>Editar usuario</h2>
                <p>Modificar información del personal DELIZ</p>
            </div>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?route=usuarios-actualizar" method="POST">

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <input type="hidden" name="id_usuario"
                   value="<?= (int)$usuario['id_usuario'] ?>">

            <div class="mb-3">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre_completo"
                       class="form-control" maxlength="100" required
                       value="<?= htmlspecialchars($usuario['nombre_completo']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre de usuario *</label>
                <input type="text" name="nombre_usuario"
                       class="form-control" maxlength="50" required
                       value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo"
                       class="form-control" maxlength="100"
                       value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="password"
                       class="form-control" minlength="8">
                <small class="text-secondary">
                    Déjala vacía si deseas conservar la contraseña actual.
                </small>
            </div>

            <div class="mb-4">
                <label class="form-label">Rol *</label>

                <select name="id_rol" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int)$rol['id_rol'] ?>"
                            <?= (int)$usuario['id_rol'] === (int)$rol['id_rol'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['nombre_rol']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <a href="index.php?route=usuarios"
                   class="btn btn-secondary w-50">Cancelar</a>

                <button type="submit" class="btn btn-deliz w-50">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>

</body>
</html>
