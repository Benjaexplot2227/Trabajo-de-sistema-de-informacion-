<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('movimientos.php');
verify_csrf();

$productId = filter_var($_POST['id_producto'] ?? null, FILTER_VALIDATE_INT);
$quantity = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$type = (string) ($_POST['tipo'] ?? '');
$observation = trim((string) ($_POST['observacion'] ?? ''));
if (!$productId || $productId < 1 || $quantity === false || !in_array($type, ['ENTRADA', 'SALIDA'], true) || strlen($observation) > 255) {
    set_flash('danger', 'Revisa el producto, tipo, cantidad y observación del movimiento.');
    redirect('movimientos.php');
}

$pdo = db();
try {
    $pdo->beginTransaction();
    $lock = $pdo->prepare('SELECT stock_actual FROM productos WHERE id_producto = ? AND estado = 1 FOR UPDATE');
    $lock->execute([$productId]);
    $current = $lock->fetchColumn();
    if ($current === false) throw new DomainException('El producto no existe o está desactivado.');
    $currentStock = (int) $current;
    if ($type === 'SALIDA' && $quantity > $currentStock) throw new DomainException('La salida supera el stock disponible. No se realizó ningún cambio.');

    $newStock = $type === 'ENTRADA' ? $currentStock + $quantity : $currentStock - $quantity;
    $insert = $pdo->prepare('INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([$type, $quantity, $observation !== '' ? $observation : null, $productId, $_SESSION['user_id']]);
    $update = $pdo->prepare('UPDATE productos SET stock_actual = ? WHERE id_producto = ?');
    $update->execute([$newStock, $productId]);
    $pdo->commit();
    set_flash('success', 'Movimiento registrado y stock actualizado.');
} catch (DomainException $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    set_flash('warning', $exception->getMessage());
} catch (PDOException $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    set_flash('danger', 'No se pudo guardar el movimiento. No se cambió el stock.');
}
redirect('movimientos.php');

