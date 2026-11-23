<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/layout.php';
require_login();

$summary = db()->query(
    'SELECT COUNT(*) AS total, SUM(CASE WHEN stock_actual < stock_minimo THEN 1 ELSE 0 END) AS stock_bajo
     FROM productos WHERE estado = 1'
)->fetch();
$recent = db()->query(
    'SELECT m.fecha_hora, m.tipo, m.cantidad, p.nombre AS producto, u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON p.id_producto = m.id_producto
     INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
     ORDER BY m.fecha_hora DESC, m.id_movimiento DESC LIMIT 5'
)->fetchAll();

page_header('Panel principal');
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><p class="eyebrow mb-1">Resumen</p><h1 class="h2 mb-1">Panel de inventario</h1><p class="text-secondary mb-0">Consulta productos, movimientos y alertas desde un solo lugar.</p></div>
    <a class="btn btn-primary" href="producto_form.php">+ Registrar producto</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="metric-card card h-100"><div class="card-body"><div class="text-secondary">Productos activos</div><div class="display-6 fw-semibold"><?= (int) ($summary['total'] ?? 0) ?></div></div></div></div>
    <div class="col-md-6"><div class="metric-card card h-100"><div class="card-body"><div class="text-secondary">Productos con stock bajo</div><div class="display-6 fw-semibold text-warning-emphasis"><?= (int) ($summary['stock_bajo'] ?? 0) ?></div></div></div></div>
</div>
<section class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-3"><h2 class="h5 mb-0">Movimientos recientes</h2><a href="movimientos.php" class="btn btn-sm btn-outline-primary">Ver movimientos</a></div>
        <?php if (!$recent): ?><p class="text-secondary mb-0">Aún no hay movimientos registrados.</p><?php else: ?>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Usuario</th></tr></thead><tbody>
            <?php foreach ($recent as $item): ?><tr><td><?= h($item['fecha_hora']) ?></td><td><?= h($item['producto']) ?></td><td><?= h(str_replace('_', ' ', $item['tipo'])) ?></td><td><?= (int) $item['cantidad'] ?></td><td><?= h($item['usuario']) ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
    </div>
</section>
<?php page_footer(); ?>
