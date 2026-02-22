<link rel="stylesheet" href="<?= base_url('assets/css/usuarios_lista.css?v=' . time()) ?>">

<main class="main-content">
    
    <div class="page-header">
        <a href="<?= base_url('usuarios/registrar') ?>" class="btn-primary" style="text-decoration: none;">
            <i class="fa fa-user-plus"></i> Nuevo Cliente
        </a>
    </div>

    <div class="form-card">
        <div class="table-container">
            <table class="elegant-table" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyUsuarios">
                    <tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando usuarios...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="modalEditarUsuario" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Editar Usuario</h2>
            <button class="btn-close-modal" type="button" onclick="cerrarModal()"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="formEditarUsuario">
                <input type="hidden" id="edit_id_usuario"> 
                <input type="hidden" id="edit_activo"> 
                
                <div class="form-section-title" style="margin-top: 0;">Datos Personales</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre(s) *</label>
                        <input type="text" id="edit_nombres" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos *</label>
                        <input type="text" id="edit_apellidos" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" id="edit_telefono">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <input type="text" id="edit_pais">
                    </div>
                    <div class="form-group">
                        <label>Ciudad</label>
                        <input type="text" id="edit_ciudad">
                    </div>
                </div>

                <div class="form-section-title">Datos de Cuenta</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Correo Electrónico *</label>
                        <input type="email" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre de Usuario *</label>
                        <input type="text" id="edit_nombre_usuario" required>
                    </div>
                    <div class="form-group">
                        <label>Rol del Sistema *</label>
                        <select id="edit_id_rol" required>
                            <option value="1">Administrador</option>
                            <option value="3">Suscriptor (Cliente)</option> 
                        </select>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 25px;">
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="btnActualizarUsuario">
                        <i class="fa fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?= base_url() ?>";
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/usuarios_lista.js?v=' . time()) ?>"></script>