<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | StreamingMF</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=' . time()) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/peliculas_lista.css?v=' . time()) ?>">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="admin-body">

<header class="main-header">
    <div class="brand">
        Streaming<span>MF</span>
    </div>

    <nav class="main-nav">
        <a href="<?= base_url('dashboard') ?>" class="active">Dashboard</a>
        <a href="<?= base_url('peliculas/registrar') ?>">Registrar Películas</a>
        <a href="<?= base_url('peliculas') ?>">Consultar Películas</a>
        <a href="<?= base_url('usuarios/registrar') ?>">Registrar Usuarios</a>
        <a href="<?= base_url('usuarios') ?>">Consultar Clientes</a>
        <a href="<?= base_url('catalogo') ?>">Ver Películas</a>
        
        <div class="nav-divider"></div>

        <a href="javascript:void(0)" onclick="logout()" class="logout-btn">
            <i class="fa fa-right-from-bracket"></i> Salir
        </a>
    </nav>
</header>

<main class="main-content">
    
</main>

</body>
</html>