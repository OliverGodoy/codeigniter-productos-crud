<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? $titulo : 'Productos' ?> - CodeIgniter CRUD</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('productos') ?>">Productos CRUD - CodeIgniter</a>
        </div>
    </nav>

    <main class="container">
        <?php if ($this->session->flashdata('mensaje')): ?>
            <div class="alert alert-success" role="alert">
                <?= $this->session->flashdata('mensaje') ?>
            </div>
        <?php endif; ?>
