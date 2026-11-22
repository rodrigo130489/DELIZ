
<?php
/** @var array $categoria */

$mensajeError = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoría | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/categorias.css">
</head>

<body>

<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Editar categoría</h2>
        <p class="text-muted">
            Modifica la información de la categoría seleccionada.
        </p>
    </div>

    <?php if ($mensajeError): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form action="index.php?route=categorias-actualizar"
                  method="POST">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <input type="hidden"
                       name="id_categoria"
                       value="<?= (int)$categoria['id_categoria'] ?>">

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nombre de categoría
                    </label>

                    <input type="text"
                           name="nombre_categoria"
                           class="form-control"
                           maxlength="100"
                           value="<?= htmlspecialchars($categoria['nombre_categoria']) ?>"
                           required>

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Descripción
                    </label>

                    <textarea name="descripcion"
                              class="form-control"
                              rows="4"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>

                </div>

                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">
                        Actualizar categoría
                    </button>

                    <a href="index.php?route=categorias"
                       class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
