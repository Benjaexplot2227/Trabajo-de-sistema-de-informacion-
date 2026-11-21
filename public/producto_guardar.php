<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('productos.php');
verify_csrf();

$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
$id = $id === false ? 0 : $id;
$code = trim((string) ($_POST['codigo'] ?? ''));
$name = trim((string) ($_POST['nombre'] ?? ''));
$description = trim((string) ($_POST['descripcion'] ?? ''));
$categoryId = filter_var($_POST['id_categoria'] ?? null, FILTER_VALIDATE_INT);
$priceRaw = (string) ($_POST['precio'] ?? '');
$stockRaw = (string) ($_POST['stock_actual'] ?? '');
$minimumRaw = (string) ($_POST['stock_minimo'] ?? '');
$errors = [];

if ($code === '' || strlen($code) > 40) $errors[] = 'El código es obligatorio y debe tener hasta 40 caracteres.';
if ($name === '' || strlen($name) > 120) $errors[] = 'El nombre es obligatorio y debe tener hasta 120 caracteres.';
if ($description !== '' && strlen($description) > 500) $errors[] = 'La descripción no puede superar 500 caracteres.';
if (!is_numeric($priceRaw) || (float) $priceRaw < 0) $errors[] = 'El precio debe ser un número igual o mayor que cero.';
$stock = filter_var($stockRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
$minimum = filter_var($minimumRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
if ($stock === false) $errors[] = 'El stock debe ser un número entero igual o mayor que cero.';
if ($minimum === false) $errors[] = 'El stock mínimo debe ser un número entero igual o mayor que cero.';
if ($categoryId === false || $categoryId === null) $errors[] = 'Selecciona una categoría válida.';
if ($errors) {
    set_flash('danger', implode(' ', $errors));
    redirect($id > 0 ? 'producto_form.php?id=' . $id : 'producto_form.php');
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $categoryCheck = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id_categoria = ? AND estado = 1');
    $categoryCheck->execute([$categoryId]);
    if ((int) $categoryCheck->fetchColumn() === 0) {
        throw new DomainException('La categoría seleccionada no está disponible.');
    }

    if ($id === 0) {
        $insert = $pdo->prepare('INSERT INTO productos (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) VALUES (?, ?, ?, ?, 0, ?, ?)');
        $insert->execute([$code, $name, $description !== '' ? $description : null, $priceRaw, $minimum, $categoryId]);
        $productId = (int) $pdo->lastInsertId();
        if ($stock > 0) {
            $movement = $pdo->prepare("INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario) VALUES ('ENTRADA', ?, 'Stock inicial', ?, ?)");
            $movement->execute([$stock, $productId, $_SESSION['user_id']]);
            $updateStock = $pdo->prepare('UPDATE productos SET stock_actual = ? WHERE id_producto = ?');
            $updateStock->execute([$stock, $productId]);
        }
        $message = 'Producto registrado correctamente.';
    } else {
        $lock = $pdo->prepare('SELECT stock_actual FROM productos WHERE id_producto = ? AND estado = 1 FOR UPDATE');
        $lock->execute([$id]);
        $current = $lock->fetchColumn();
        if ($current === false) throw new DomainException('No se encontró el producto activo.');
        $currentStock = (int) $current;

        $update = $pdo->prepare('UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, precio = ?, stock_minimo = ?, id_categoria = ? WHERE id_producto = ?');
        $update->execute([$code, $name, $description !== '' ? $description : null, $priceRaw, $minimum, $categoryId, $id]);
        $difference = (int) $stock - $currentStock;
        if ($difference !== 0) {
            $type = $difference > 0 ? 'AJUSTE_ENTRADA' : 'AJUSTE_SALIDA';
            $quantity = abs($difference);
            $movement = $pdo->prepare('INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario) VALUES (?, ?, ?, ?, ?)');
            $movement->execute([$type, $quantity, 'Ajuste de stock desde la edición del producto', $id, $_SESSION['user_id']]);
            $updateStock = $pdo->prepare('UPDATE productos SET stock_actual = ? WHERE id_producto = ?');
            $updateStock->execute([$stock, $id]);
        }
        $message = 'Producto actualizado correctamente.';
    }

    $pdo->commit();
    set_flash('success', $message);
} catch (DomainException $exception) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    set_flash('warning', $exception->getMessage());
    redirect($id > 0 ? 'producto_form.php?id=' . $id : 'producto_form.php');
} catch (PDOException $exception) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    $message = $exception->getCode() === '23000'
        ? 'No se guardó: el código ya está en uso o los datos incumplen una relación.'
        : 'No se pudo guardar el producto. Revisa la conexión y vuelve a intentarlo.';
    set_flash('danger', $message);
    redirect($id > 0 ? 'producto_form.php?id=' . $id : 'producto_form.php');
}

redirect('productos.php');
