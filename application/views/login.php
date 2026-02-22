<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>StreamingMF</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
        <script>
        const BASE_URL = "<?= base_url() ?>";

        if (localStorage.getItem('token')) {
            window.location.href = BASE_URL + "dashboard";
        }
    </script>
</head>
<body class="auth-body">

<div class="login-card">
    <img src="<?= base_url('assets/img/streamingmf.png') ?>" class="logo">

    <h2>Iniciar sesión</h2>

    <div class="input-group">
        <i class="fa fa-envelope"></i>
        <input type="email" id="email" placeholder="Correo electrónico">
    </div>

    <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" id="password" placeholder="Contraseña">
    </div>

    <button onclick="login()">Acceder</button>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<script>
const BASE_URL = "<?= base_url() ?>";
</script>
</body>
</html>