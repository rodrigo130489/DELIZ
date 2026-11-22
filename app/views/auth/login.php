
<?php
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | DELIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="assets/css/auth.css">
</head>

<body class="login-body">

<div class="login-container">

    <div class="login-brand">
        <div class="brand-icon">D</div>

        <h1>DELIZ</h1>

        <p>FOOD TRUCK · COMIDA RÁPIDA</p>

        <div class="brand-decoration">
            <span>BURGER</span>
            <span>•</span>
            <span>FRIES</span>
            <span>•</span>
            <span>DELICIOUS</span>
        </div>
    </div>

    <div class="login-card">

        <div class="text-center mb-4">
            <h2>Bienvenido</h2>
            <p class="text-muted">
                Ingresa tus datos para acceder al sistema
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=autenticar"
              method="POST">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= escape(csrfToken()) ?>">

            <div class="mb-3">
                <label class="form-label" for="nombre_usuario">
                    Nombre de usuario
                </label>

                <input type="text"
                       id="nombre_usuario" name="nombre_usuario"
                       class="form-control"
                       placeholder="Ingresa tu usuario"
                       maxlength="50"
                       autocomplete="username"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">
                    Contraseña
                </label>

                <input type="password"
                       id="password" name="password"
                       class="form-control"
                       placeholder="Ingresa tu contraseña"
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Ingresar al sistema
            </button>

        </form>

        <div class="login-footer">
            DELIZ © <?= date('Y') ?> · Sistema de gestión y ventas
        </div>

    </div>

</div>

</body>
</html>
