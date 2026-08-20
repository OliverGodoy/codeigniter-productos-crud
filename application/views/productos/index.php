<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Productos</h1>
    <a href="<?= site_url('productos/crear') ?>" class="btn btn-primary">Nuevo producto</a>
</div>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No hay productos registrados todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= $producto->id ?></td>
                    <td><?= html_escape($producto->nombre) ?></td>
                    <td>Q<?= number_format($producto->precio, 2) ?></td>
                    <td><?= $producto->stock ?></td>
                    <td class="text-end">
                        <a href="<?= site_url('productos/editar/' . $producto->id) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <?= form_open('productos/eliminar/' . $producto->id, [
                            'class' => 'd-inline',
                            'onsubmit' => "return confirm('¿Eliminar este producto?');",
                        ]) ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
