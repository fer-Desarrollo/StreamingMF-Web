<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>StreamingMF</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="splash-body">

    <div class="splash-container">
        <img src="<?= base_url('assets/img/streamingmf.png') ?>" class="logo-splash">
    </div>

    <script>
        setTimeout(() => {
            window.location.href = "<?= base_url('auth/login') ?>";
        }, 3000);
    </script>

</body>
</html>