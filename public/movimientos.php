<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';
require_login();

$products = db()->query('SELECT id_producto, codigo, nombre, stock_actual FROM productos WHERE estado = 1 ORDER BY nombre')->fetchAll();
$movements = db()->query(
    'SELECT m.fecha_hora, m.tipo, m.cantidad, m.observacion, p.codigo, p.nombre AS producto, u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON p.id_producto = m.id_producto
     INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
     ORDER BY m.fecha_hora DESC, m.id_movimiento DESC LIMIT 100'
)->fetchAll();

page_header('Movimientos');
?>
<div class="mb-4"><p class="eyebrow mb-1">Existencias</p><h1 class="h2 mb-1">Movimientos de inventario</h1><p class="text-secondary mb-0">Las entradas suman unidades y las salidas las descuentan del stock.</p></div>
<section class="card shadow-sm mb-4"><div class="card-body">
    <h2 class="h5 mb-3">Registrar movimiento</h2>
    <?php if (!$products): ?><div class="alert alert-info mb-0">Primero registra un producto activo para ingresar movimientos.</div><?php else: ?>
    <form class="row g-3 align-items-end" method="post" action="movimiento_guardar.php" data-validate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="col-md-5"><label class="form-label" for="producto">Producto</label><select class="form-select" id="producto" name="id_producto" required><option value="">Selecciona un producto</option><?php foreach ($products as $product): ?><option value="<?= (int) $product['id_producto'] ?>"><?= h($product['codigo'] . ' · ' . $product['nombre'] . ' (stock ' . $product['stock_actual'] . ')') ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label" for="tipo">Tipo</label><select class="form-select" id="tipo" name="tipo" required><option value="ENTRADA">Entrada</option><option value="SALIDA">Salida</option></select></div>
        <div class="col-md-2"><label class="form-label" for="cantidad">Cantidad</label><input class="form-control" id="cantidad" type="number" name="cantidad" min="1" step="1" required></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Guardar</button></div>
        <div class="col-12"><label class="form-label" for="observacion">Observación <span class="text-secondary">(opcional)</span></label><input class="form-control" id="observacion" name="observacion" maxlength="255"></div>
    </form>
    <?php endif; ?>
</div></section>
<section class="card shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Historial reciente</h2>
    <?php if (!$movements): ?><p class="text-secondary mb-0">Aún no hay movimientos.</p><?php else: ?>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Observación</th><th>Responsable</th></tr></thead><tbody>
        <?php foreach ($movements as $movement): ?><tr><td class="text-nowrap"><?= h($movement['fecha_hora']) ?></td><td><?= h($movement['codigo'] . ' · ' . $movement['producto']) ?></td><td><?= h(str_replace('_', ' ', $movement['tipo'])) ?></td><td><?= (int) $movement['cantidad'] ?></td><td><?= h($movement['observacion'] ?? '—') ?></td><td><?= h($movement['usuario']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
</div></section>
<?php page_footer(); ?>
