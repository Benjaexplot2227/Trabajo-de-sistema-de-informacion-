<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';
require_login();

$search = trim((string) ($_GET['q'] ?? ''));
$status = (string) ($_GET['estado'] ?? 'activos');
$statusClause = match ($status) {
    'inactivos' => 'p.estado = 0',
    'todos' => '1 = 1',
    default => 'p.estado = 1',
};
$sql = 'SELECT p.*, c.nombre AS categoria
        FROM productos p INNER JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE ' . $statusClause;
$params = [];
if ($search !== '') {
    $sql .= ' AND (p.nombre LIKE ? OR p.codigo LIKE ? OR c.nombre LIKE ?)';
    $term = '%' . $search . '%';
    $params = [$term, $term, $term];
}
$sql .= ' ORDER BY p.estado DESC, p.nombre ASC';
$statement = db()->prepare($sql);
$statement->execute($params);
$products = $statement->fetchAll();

page_header('Productos');
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><p class="eyebrow mb-1">Catálogo</p><h1 class="h2 mb-1">Productos</h1><p class="text-secondary mb-0">Busca, revisa y mantén actualizado el catálogo.</p></div>
    <a class="btn btn-primary" href="producto_form.php">+ Registrar producto</a>
</div>
<form class="card card-body shadow-sm mb-3" method="get">
    <div class="row g-2 align-items-end">
        <div class="col-md-7"><label class="form-label" for="q">Buscar por nombre, código o categoría</label><input class="form-control" id="q" name="q" value="<?= h($search) ?>" placeholder="Ej.: audífonos o ACC-001"></div>
        <div class="col-md-3"><label class="form-label" for="estado">Mostrar</label><select class="form-select" id="estado" name="estado"><option value="activos" <?= $status === 'activos' ? 'selected' : '' ?>>Activos</option><option value="inactivos" <?= $status === 'inactivos' ? 'selected' : '' ?>>Desactivados</option><option value="todos" <?= $status === 'todos' ? 'selected' : '' ?>>Todos</option></select></div>
        <div class="col-md-2 d-grid"><button class="btn btn-outline-primary">Buscar</button></div>
    </div>
</form>
<section class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
    <tbody>
    <?php if (!$products): ?><tr><td colspan="7" class="text-center text-secondary py-4">No se encontraron productos.</td></tr><?php endif; ?>
    <?php foreach ($products as $product): $low = (int) $product['estado'] === 1 && (int) $product['stock_actual'] < (int) $product['stock_minimo']; ?>
        <tr>
            <td class="text-nowrap"><?= h($product['codigo']) ?></td>
            <td><div class="fw-semibold"><?= h($product['nombre']) ?></div><?php if ($low): ?><span class="badge text-bg-warning mt-1">Stock bajo</span><?php endif; ?></td>
            <td><?= h($product['categoria']) ?></td>
            <td><?= h(format_money($product['precio'])) ?></td>
            <td><?= (int) $product['stock_actual'] ?> <span class="text-secondary small">/ mín. <?= (int) $product['stock_minimo'] ?></span></td>
            <td><span class="badge <?= (int) $product['estado'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (int) $product['estado'] === 1 ? 'Activo' : 'Desactivado' ?></span></td>
            <td class="text-end text-nowrap">
                <?php if ((int) $product['estado'] === 1): ?>
                    <a class="btn btn-sm btn-outline-primary" href="producto_form.php?id=<?= (int) $product['id_producto'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="producto_desactivar.php" data-confirm="¿Desactivar este producto? Se conservarán sus movimientos.">
                        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $product['id_producto'] ?>">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">Desactivar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table></div></section>
<?php page_footer(); ?>
