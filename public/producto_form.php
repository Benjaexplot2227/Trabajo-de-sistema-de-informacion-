<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = ['id_producto' => 0, 'codigo' => '', 'nombre' => '', 'descripcion' => '', 'precio' => '0', 'stock_actual' => 0, 'stock_minimo' => 0, 'id_categoria' => ''];
if ($id > 0) {
    $statement = db()->prepare('SELECT * FROM productos WHERE id_producto = ? AND estado = 1');
    $statement->execute([$id]);
    $product = $statement->fetch();
    if (!$product) {
        set_flash('warning', 'No se encontró el producto activo solicitado.');
        redirect('productos.php');
    }
}
$categories = db()->query('SELECT id_categoria, nombre FROM categorias WHERE estado = 1 ORDER BY nombre')->fetchAll();

page_header($id > 0 ? 'Editar producto' : 'Registrar producto');
?>
<div class="form-shell">
    <div class="mb-4"><p class="eyebrow mb-1">Catálogo</p><h1 class="h2 mb-1"><?= $id > 0 ? 'Editar producto' : 'Registrar producto' ?></h1><p class="text-secondary mb-0">Los cambios de existencias quedan registrados en el historial.</p></div>
    <form class="card card-body shadow-sm" method="post" action="producto_guardar.php" data-validate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $product['id_producto'] ?>">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label" for="codigo">Código</label><input class="form-control" id="codigo" name="codigo" maxlength="40" value="<?= h($product['codigo']) ?>" required></div>
            <div class="col-md-8"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" maxlength="120" value="<?= h($product['nombre']) ?>" required></div>
            <div class="col-md-6"><label class="form-label" for="categoria">Categoría</label><select class="form-select" id="categoria" name="id_categoria" required><option value="">Selecciona una categoría</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id_categoria'] ?>" <?= (int) $product['id_categoria'] === (int) $category['id_categoria'] ? 'selected' : '' ?>><?= h($category['nombre']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label" for="precio">Precio</label><input class="form-control" id="precio" type="number" name="precio" min="0" step="0.01" value="<?= h((string) $product['precio']) ?>" required></div>
            <div class="col-md-3"><label class="form-label" for="stock_minimo">Stock mínimo</label><input class="form-control" id="stock_minimo" type="number" name="stock_minimo" min="0" step="1" value="<?= (int) $product['stock_minimo'] ?>" required></div>
            <div class="col-md-4"><label class="form-label" for="stock_actual">Stock actual</label><input class="form-control" id="stock_actual" type="number" name="stock_actual" min="0" step="1" value="<?= (int) $product['stock_actual'] ?>" required><div class="form-text"><?= $id > 0 ? 'Si cambia, se registrará un ajuste en movimientos.' : 'Un stock inicial mayor que cero crea una entrada inicial.' ?></div></div>
            <div class="col-12"><label class="form-label" for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500"><?= h($product['descripcion']) ?></textarea></div>
        </div>
        <div class="d-flex gap-2 justify-content-end mt-4"><a class="btn btn-outline-secondary" href="productos.php">Cancelar</a><button class="btn btn-primary" type="submit">Guardar producto</button></div>
    </form>
</div>
<?php page_footer(); ?>
