
<?php

$nombre = htmlspecialchars(
    $usuario['nombre_completo'],
    ENT_QUOTES,
    'UTF-8'
);

$rol = htmlspecialchars(
    $usuario['nombre_rol'],
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

<div class="app-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="sidebar-logo">D</div>
            <div>
                <h3>DELIZ</h3>
                <small>FOOD TRUCK</small>
            </div>
        </div>

        <div class="sidebar-label">MENÚ PRINCIPAL</div>

        <nav class="sidebar-menu">
            <a href="index.php?route=dashboard" class="active">
                <span>▦</span> Dashboard
            </a>

            <?php if ((int)$usuario['id_rol'] === 1): ?>
                <a href="index.php?route=usuarios">
                    <span>♙</span> Usuarios
                </a>
            <?php endif; ?>


        </nav>

        <div class="sidebar-bottom">
            <form method="post" action="index.php?route=logout">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                <button type="submit" class="logout-link w-100">Cerrar sesión</button>
            </form>
        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <h2>Panel de control</h2>
                <p>Administración del negocio DELIZ</p>
            </div>

            <div class="user-info">
                <div class="user-avatar">D</div>
                <div>
                    <strong><?= $nombre ?></strong>
                    <small><?= $rol ?></small>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <h1>¡Bienvenido a DELIZ!</h1>
            <p>Gestiona los pedidos, productos y operaciones de tu negocio.</p>
        </section>

        <section class="dashboard-cards">

            <article class="stat-card">
                <div class="stat-icon orange">🛒</div>
                <div>
                    <span>Pedidos</span>
                    <h3>Gestión</h3>
                    <small>Control de pedidos</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon green">🍔</div>
                <div>
                    <span>Productos</span>
                    <h3>Catálogo</h3>
                    <small>Comidas y bebidas</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon blue">📦</div>
                <div>
                    <span>Inventario</span>
                    <h3>Control</h3>
                    <small>Existencias e insumos</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon purple">📊</div>
                <div>
                    <span>Reportes</span>
                    <h3>Consultas</h3>
                    <small>Información del negocio</small>
                </div>
            </article>

        </section>

        <section class="content-panel">
            <h3>Resumen del sistema</h3>
            <p>
                Bienvenido al sistema de gestión y ventas de comida rápida
                Food Truck DELIZ.
            </p>
            <p>
                Desde el menú lateral podrás acceder a los módulos
                correspondientes a tu rol.
            </p>
        </section>

        <footer class="dashboard-footer">
            DELIZ © <?= date('Y') ?> · Sistema de gestión y ventas
        </footer>

    </main>

</div>

</body>
</html>
