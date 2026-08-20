<div class ="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tarifas</h1>
    <a href="<?= site_url('tarifas/crear') ?>" class="btn btn-primary">Nueva tarifa</a>
</div>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Precio por metro cúbico</th>
            <th>Consumo mínimo</th>
            <th>Activo</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tarifas)): ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No hay tarifas registradas todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tarifas as $tarifa): ?>
                <tr>
                    <td><?= $tarifa->id ?></td>
                    <td><?= html_escape($tarifa->nombre) ?></td>
                    <td>Q<?= number_format($tarifa->precio_por_metro_cubico, 2) ?></td>
                    <td><?= $tarifa->consumo_minimo ?> m³</td>
                    <td class="text-center">
                        <?php if ($tarifa->activa): ?>
                            <span class="badge bg-success">Sí</span>
                        <?php else: ?>
                            <span class="badge bg-danger">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= site_url('tarifas/editar/' . $tarifa->id) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <?= form_open('tarifas/eliminar/' . $tarifa->id, [
                            'class' => 'd-inline',
                            'onsubmit' => "return confirm('¿Eliminar esta tarifa?');",
                        ]) ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
