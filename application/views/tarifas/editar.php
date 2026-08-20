<h1 class="h3 mb-0">Editar tarifa</h1>

<?php if (validation_errors()): ?>
    <div class="alert alert-danger">
        <?= validation_errors('<p class="mb-0">', '</p>') ?>
    </div>
<?php endif; ?>

<?= form_open('tarifas/editar/' . $tarifa->id, ['class' => 'bg-white p-4 rounded shadow-sm']) ?>
    <div class="row g-3">
        <div class="col-md-6">
            <?= form_label('Nombre', 'nombre', ['class' => 'form-label']) ?>
            <?= form_input([
                'name' => 'nombre',
                'id' => 'nombre',
                'class' => 'form-control',
                'value' => set_value('nombre', $tarifa->nombre),
                'maxlength' => 100,
                'required' => 'required',
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= form_label('Precio por metro cúbico', 'precio_por_metro_cubico', ['class' => 'form-label']) ?>
            <input type="number" step="0.01" min="0" name="precio_por_metro_cubico" id="precio_por_metro_cubico"
                   class="form-control" value="<?= set_value('precio_por_metro_cubico', $tarifa->precio_por_metro_cubico) ?>" required>
        </div>

        <div class="col-md-6">
            <?= form_label('Consumo mínimo (m³)', 'consumo_minimo', ['class' => 'form-label']) ?>
            <input type="number" min="0" name="consumo_minimo" id="consumo_minimo" class="form-control"
                   value="<?= set_value('consumo_minimo', $tarifa->consumo_minimo) ?>" required>
        </div>

        <div class="col-md-6">
            <?= form_label('Activo', 'activa', ['class' => 'form-label d-block']) ?>
            <input type="hidden" name="activa" value="0">
            <div class="form-check form-switch">
                <?= form_checkbox([
                    'name' => 'activa',
                    'id' => 'activa',
                    'class' => 'form-check-input',
                    'value' => 1,
                    'checked' => set_checkbox('activa', 1, $tarifa->activa == 1),
                ]) ?>
                <label for="activa" class="form-check-label">Sí</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="<?= site_url('tarifas') ?>" class="btn btn-outline-secondary">Cancelar</a>
    </div>

<?= form_close() ?>