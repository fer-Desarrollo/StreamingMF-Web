<link rel="stylesheet" href="<?= base_url('assets/css/usuarios.css?v=' . time()) ?>">

<main class="main-content">

    <div class="form-card">
        <form id="formRegistrarUsuario">
            
            <div class="form-section-title">Datos Personales</div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre(s) *</label>
                    <input type="text" id="nombres" placeholder="Ej. Iván Mauricio" required>
                </div>
                
                <div class="form-group">
                    <label>Apellido Paterno *</label>
                    <input type="text" id="apellido_paterno" placeholder="Ej. Hernández" required>
                </div>

                <div class="form-group">
                    <label>Apellido Materno</label>
                    <input type="text" id="apellido_materno" placeholder="Ej. Vidal">
                </div>
            </div>

            <div class="form-section-title" style="margin-top: 30px;">Datos de Cuenta</div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Correo Electrónico *</label>
                    <input type="email" id="email" placeholder="Ej. mauricio0411vidal@gmail.com" required>
                </div>

                <div class="form-group">
                    <label>Nombre de Usuario *</label>
                    <input type="text" id="nombre_usuario" placeholder="Ej. ivan_hern" required>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 40px;">
                <button type="submit" class="btn-primary" id="btnGuardarUsuario">
                    <i class="fa fa-paper-plane"></i> Registrar y Enviar Correo
                </button>
            </div>

        </form>
    </div>
</main>

<script>
    const BASE_URL = "<?= base_url() ?>";
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/usuarios_registrar.js?v=' . time()) ?>"></script>