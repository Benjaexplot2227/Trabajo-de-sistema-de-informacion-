<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';

if (!empty($_SESSION['user_id'])) redirect('dashboard.php');
$userCount = (int) db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
if ($userCount === 0) redirect('setup.php');

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['correo'] ?? ''));
    $password = (string) ($_POST['clave'] ?? '');
    $statement = db()->prepare('SELECT id_usuario, nombre, clave_hash FROM usuarios WHERE correo = ? AND estado = 1 LIMIT 1');
    $statement->execute([$email]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['clave_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        redirect('dashboard.php');
    }
    $error = 'El correo o la contraseña no son correctos.';
}

page_header('Iniciar sesión');
?>
<div class="auth-card card shadow-sm mx-auto">
    <div class="card-body p-4 p-md-5">
        <p class="eyebrow mb-2">Control de inventario</p>
        <h1 class="h3 mb-2">Bienvenido a TecnoStock</h1>
        <p class="text-secondary mb-4">Inicia sesión para administrar productos y existencias.</p>
        <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= h($error) ?></div><?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <div class="mb-3"><label class="form-label" for="correo">Correo</label><input class="form-control" id="correo" type="email" name="correo" value="<?= h($email) ?>" autocomplete="username" required></div>
            <div class="mb-4"><label class="form-label" for="clave">Contraseña</label><input class="form-control" id="clave" type="password" name="clave" autocomplete="current-password" required></div>
            <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
        </form>
    </div>
</div>
<?php page_footer(); ?>
