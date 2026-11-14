<?php
declare(strict_types=1);

function page_header(string $title): void
{
    $flash = take_flash();
    $loggedIn = !empty($_SESSION['user_id']);
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h($title) ?> · TecnoStock</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link href="assets/css/tecnostock.css" rel="stylesheet">
    </head>
    <body>
    <?php if ($loggedIn): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-semibold" href="dashboard.php">TecnoStock</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mostrar navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <div class="navbar-nav me-auto">
                        <a class="nav-link" href="productos.php">Productos</a>
                        <a class="nav-link" href="movimientos.php">Movimientos</a>
                    </div>
                    <span class="navbar-text me-3"><?= h($_SESSION['user_name'] ?? 'Usuario') ?></span>
                    <form method="post" action="logout.php" class="d-flex">
                        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                        <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    <main class="container py-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?= h($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= h($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>
    <?php
}
