<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';

$userCount = (int) db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
if ($userCount > 0) {
    redirect('login.php');
}

$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['nombre'] ?? ''));
    $email = trim((string) ($_POST['correo'] ?? ''));
    $password = (string) ($_POST['clave'] ?? '');
    $confirmation = (string) ($_POST['confirmar_clave'] ?? '');

    if ($name === '') $errors[] = 'Escribe el nombre del encargado.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Escribe un correo válido.';
    if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    if ($password !== $confirmation) $errors[] = 'Las contraseñas no coinciden.';

    if (!$errors) {
        $statement = db()->prepare('INSERT INTO usuarios (nombre, correo, clave_hash) VALUES (?, ?, ?)');
        $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        set_flash('success', 'Cuenta inicial creada. Ya puedes iniciar sesión.');
        redirect('login.php');
    }
}

page_header('Crear cuenta inicial');
?>
<div class="auth-card card shadow-sm mx-auto">
    <div class="card-body p-4 p-md-5">
        <p class="eyebrow mb-2">Configuración inicial</p>
        <h1 class="h3 mb-2">Crear cuenta del encargado</h1>
        <p class="text-secondary mb-4">Este formulario solo aparece mientras no exista ningún usuario.</p>
        <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="mb-3"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" maxlength="100" value="<?= h($name) ?>" required></div>
            <div class="mb-3"><label class="form-label" for="correo">Correo</label><input class="form-control" id="correo" type="email" name="correo" maxlength="150" value="<?= h($email) ?>" required></div>
            <div class="mb-3"><label class="form-label" for="clave">Contraseña</label><input class="form-control" id="clave" type="password" name="clave" minlength="8" required><div class="form-text">Usa al menos 8 caracteres.</div></div>
            <div class="mb-4"><label class="form-label" for="confirmar_clave">Confirmar contraseña</label><input class="form-control" id="confirmar_clave" type="password" name="confirmar_clave" required></div>
            <button class="btn btn-primary w-100" type="submit">Crear cuenta</button>
        </form>
    </div>
</div>
<?php page_footer(); ?>
