
<?php
/** @var array $productos */

$mensaje = $_SESSION['mensaje'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['mensaje'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/productos.css">
</head>

<body>

<div class="container-fluid py-4 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="fw-bold mb-1">Gestión de productos</h2>
            <p class="text-muted mb-0">
                Administra los productos del Food Truck DELIZ.
            </p>
        </div>

        <a href="index.php?route=productos-crear"
           class="btn btn-primary">
            + Nuevo producto
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
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio (Bs.)</th>
                            <th>Disponibilidad</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($productos)): ?>

                        <?php foreach ($productos as $producto): ?>

                            <tr>
                                <td>
                                    <?= (int)$producto['id_producto'] ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($producto['nombre_producto']) ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($producto['descripcion'] ?? '') ?>
                                    </small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría') ?>
                                </td>

                                <td class="fw-semibold">
                                    Bs. <?= number_format((float)$producto['precio'], 2) ?>
                                </td>

                                <td>
                                    <?php if ((int)$producto['disponible'] === 1): ?>
                                        <span class="badge bg-success">
                                            Disponible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            No disponible
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($producto['estado'] === 'Activo'): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">

                                    <a href="index.php?route=productos-editar&id=<?= (int)$producto['id_producto'] ?>"
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>

                                    <form action="index.php?route=productos-estado"
                                          method="POST"
                                          class="d-inline">

                                        <input type="hidden"
                                               name="csrf_token"
                                               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                                        <input type="hidden"
                                               name="id_producto"
                                               value="<?= (int)$producto['id_producto'] ?>">

                                        <?php if ($producto['estado'] === 'Activo'): ?>

                                            <input type="hidden"
                                                   name="estado"
                                                   value="Inactivo">

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Deseas desactivar este producto?')">
                                                Desactivar
                                            </button>

                                        <?php else: ?>

                                            <input type="hidden"
                                                   name="estado"
                                                   value="Activo">

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
                            <td colspan="7" class="text-center py-4">
                                No hay productos registrados.
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
