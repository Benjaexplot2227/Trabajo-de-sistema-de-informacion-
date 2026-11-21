<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('productos.php');
verify_csrf();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('danger', 'No se recibió un producto válido.');
    redirect('productos.php');
}
$statement = db()->prepare('UPDATE productos SET estado = 0 WHERE id_producto = ? AND estado = 1');
$statement->execute([$id]);
set_flash($statement->rowCount() ? 'success' : 'warning', $statement->rowCount() ? 'Producto desactivado. Se conservaron sus movimientos.' : 'El producto ya estaba desactivado o no existe.');
redirect('productos.php');
