<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios | DELIZ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<main class="container py-4">
    <a href="index.php?route=dashboard">Volver al panel</a>
    <h1>Usuarios</h1>
    <?php if (!$lista): ?><p>No hay usuarios registrados.</p><?php endif; ?>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th></tr></thead>
            <tbody>
            <?php foreach ($lista as $fila): ?>
                <tr>
                    <?php foreach (['nombre_completo', 'nombre_usuario', 'correo', 'nombre_rol', 'estado'] as $campo): ?>
                        <td><?= escape((string) $fila[$campo]) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>

