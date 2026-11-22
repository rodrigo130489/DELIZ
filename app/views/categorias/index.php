
<?php
/** @var array $categorias */

$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['mensaje'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/categorias.css">
</head>

<body>

<div class="container-fluid py-4 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="fw-bold mb-1">Gestión de categorías</h2>
            <p class="text-muted mb-0">
                Administra las categorías de productos de DELIZ.
            </p>
        </div>

        <a href="index.php?route=categorias-crear"
           class="btn btn-primary">
            + Nueva categoría
        </a>

    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre de categoría</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($categorias)): ?>

                        <?php foreach ($categorias as $categoria): ?>

                            <tr>
                                <td>
                                    <?= htmlspecialchars((string)$categoria['id_categoria']) ?>
                                </td>

                                <td class="fw-semibold">
                                    <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($categoria['descripcion'] ?? '') ?>
                                </td>

                                <td>
                                    <?php if ($categoria['estado'] === 'Activa'): ?>
                                        <span class="badge bg-success">
                                            Activa
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            Inactiva
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">

                                    <a href="index.php?route=categorias-editar&id=<?= (int)$categoria['id_categoria'] ?>"
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>

                                    <form action="index.php?route=categorias-estado"
                                          method="POST"
                                          class="d-inline">

                                        <input type="hidden"
                                               name="csrf_token"
                                               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                                        <input type="hidden"
                                               name="id_categoria"
                                               value="<?= (int)$categoria['id_categoria'] ?>">

                                        <?php if ($categoria['estado'] === 'Activa'): ?>

                                            <input type="hidden"
                                                   name="estado"
                                                   value="Inactiva">

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Deseas desactivar esta categoría?')">
                                                Desactivar
                                            </button>

                                        <?php else: ?>

                                            <input type="hidden"
                                                   name="estado"
                                                   value="Activa">

                                            <button type="submit"
                                                    class="btn btn-sm btn-success">
                                                Activar
                                            </button>

                                        <?php endif; ?>

                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No hay categorías registradas.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <div class="mt-3">
        <a href="index.php?route=dashboard"
           class="btn btn-outline-secondary">
            Volver al dashboard
        </a>
    </div>

</div>

</body>
</html>
