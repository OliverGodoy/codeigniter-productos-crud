<h1 class="h3 mb-3">Editar producto</h1>

<?php if (validation_errors()): ?>
    <div class="alert alert-danger">
        <?= validation_errors('<p class="mb-0">', '</p>') ?>
    </div>
<?php endif; ?>

<?= form_open('productos/editar/' . $producto->id, ['class' => 'bg-white p-4 rounded shadow-sm']) ?>

    <div class="mb-3">
        <?= form_label('Nombre', 'nombre', ['class' => 'form-label']) ?>
        <?= form_input([
            'name' => 'nombre',
            'id' => 'nombre',
            'class' => 'form-control',
            'value' => set_value('nombre', $producto->nombre),
            'maxlength' => 100,
            'required' => 'required',
        ]) ?>
    </div>

    <div class="mb-3">
        <?= form_label('Precio', 'precio', ['class' => 'form-label']) ?>
        <input type="number" step="0.01" min="0" name="precio" id="precio" class="form-control"
               value="<?= set_value('precio', $producto->precio) ?>" required>
    </div>

    <div class="mb-3">
        <?= form_label('Stock', 'stock', ['class' => 'form-label']) ?>
        <input type="number" min="0" name="stock" id="stock" class="form-control"
               value="<?= set_value('stock', $producto->stock) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="<?= site_url('productos') ?>" class="btn btn-outline-secondary">Cancelar</a>

<?= form_close() ?>
