<main class="main-content">
    
    <div class="page-header">
        <div>
            <h1>Panel de Control</h1>
            <p>Selecciona una acción para comenzar a administrar el sistema.</p>
        </div>
    </div>

    <div class="menu-grid">
        
        <a href="<?= base_url('peliculas/registrar') ?>" class="menu-card">
            <div class="icon-wrapper">
                <i class="fa fa-file-video"></i>
            </div>
            <h3>Registrar Películas</h3>
            <p>Añadir nuevos títulos al sistema</p>
        </a>

        <a href="<?= base_url('peliculas') ?>" class="menu-card">
            <div class="icon-wrapper">
                <i class="fa fa-table-list"></i>
            </div>
            <h3>Consultar Películas</h3>
            <p>Gestionar y editar el catálogo</p>
        </a>

        <a href="<?= base_url('usuarios/registrar') ?>" class="menu-card">
            <div class="icon-wrapper">
                <i class="fa fa-user-plus"></i>
            </div>
            <h3>Registrar Usuarios</h3>
            <p>Crear cuentas para nuevos clientes</p>
        </a>

        <a href="<?= base_url('usuarios') ?>" class="menu-card">
            <div class="icon-wrapper">
                <i class="fa fa-users"></i>
            </div>
            <h3>Consultar Clientes</h3>
            <p>Ver y administrar suscriptores</p>
        </a>



        <a href="<?= base_url('catalogo') ?>" class="menu-card">
            <div class="icon-wrapper">
                <i class="fa fa-circle-play"></i>
            </div>
            <h3>Ver Películas</h3>
            <p>Explorar el catálogo como cliente</p>
        </a>

    </div>

</main>