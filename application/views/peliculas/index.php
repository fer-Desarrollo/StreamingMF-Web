
<main class="main-content">
    
    <div class="page-header">
        <a href="<?= base_url('peliculas/registrar') ?>" class="btn-primary" style="text-decoration: none;">
            <i class="fa fa-plus"></i> Nueva Película
        </a>
    </div>

    <div class="form-card">
        <div class="table-container">
            <table class="elegant-table" id="tablaPeliculas">
                <thead>
                    <tr>
                        <th>Póster</th>
                        <th>Título</th>
                        <th>Año</th>
                        <th>Clasificación</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyPeliculas">
                    <tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando películas...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="modalEditar" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Editar Película</h2>
            <button class="btn-close-modal" onclick="cerrarModal()"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="formEditarPelicula">
                <input type="hidden" id="edit_id"> 
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" id="edit_titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Título Original</label>
                        <input type="text" id="edit_titulo_original">
                    </div>
                    <div class="form-group">
                        <label>Año de Estreno *</label>
                        <input type="number" id="edit_anio" required>
                    </div>
                    <div class="form-group">
                        <label>Duración (Min) *</label>
                        <input type="number" id="edit_duracion" required>
                    </div>
                    <div class="form-group">
                        <label>Clasificación *</label>
                        <select id="edit_clasificacion" required>
                            <option value="G">G</option>
                            <option value="PG">PG</option>
                            <option value="PG-13">PG-13</option>
                            <option value="R">R</option>
                            <option value="NC-17">NC-17</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Idioma Original</label>
                        <input type="text" id="edit_idioma">
                    </div>
                    <div class="form-group">
                        <label>País Producción</label>
                        <input type="text" id="edit_pais">
                    </div>
                    <div class="form-group">
                        <label>URL Tráiler</label>
                        <input type="url" id="edit_trailer">
                    </div>
                </div>

                <div class="form-group full-width" style="margin-top: 15px;">
                    <label>Sinopsis *</label>
                    <textarea id="edit_sinopsis" rows="4" required></textarea>
                </div>
                
                <div class="form-group full-width" style="margin-top: 15px;">
                    <label>URL Video</label>
                    <input type="url" id="edit_video">
                </div>

                <div class="form-actions" style="margin-top: 25px;">
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="btnActualizar">
                        <i class="fa fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Variables globales para JS
    const BASE_URL = "<?= base_url() ?>";
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/peliculas_lista.js?v=' . time()) ?>"></script>