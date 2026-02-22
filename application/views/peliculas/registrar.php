<link rel="stylesheet" href="<?= base_url('assets/css/peliculas.css?v=' . time()) ?>">

<main class="main-content">
    <div class="form-card">
        <form id="formPelicula">
            
            <div class="form-section-title">Información Principal</div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Título *</label>
                    <input type="text" id="titulo" placeholder="Ej. Inception" required>
                </div>
                <div class="form-group">
                    <label>Título Original</label>
                    <input type="text" id="titulo_original" placeholder="Ej. Inception">
                </div>

                <div class="form-group">
                    <label>Año de Estreno *</label>
                    <input type="number" id="anio_estreno" placeholder="Ej. 2010" required>
                </div>
                <div class="form-group">
                    <label>Duración (Minutos) *</label>
                    <input type="number" id="duracion_min" placeholder="Ej. 148" required>
                </div>
                
                <div class="form-group">
                    <label>Clasificación *</label>
                    <select id="clasificacion" required>
                        <option value="">Seleccione...</option>
                        <option value="G">G (Público General)</option>
                        <option value="PG">PG (Guía Parental)</option>
                        <option value="PG-13">PG-13 (Mayores de 13)</option>
                        <option value="R">R (Restringido)</option>
                        <option value="NC-17">NC-17 (Adultos)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>País de Producción</label>
                    <input type="text" id="pais_produccion" placeholder="Ej. Estados Unidos">
                </div>
                <div class="form-group">
                    <label>Idioma Original</label>
                    <input type="text" id="idioma_original" placeholder="Ej. Inglés">
                </div>
            </div>

            <div class="form-group full-width" style="margin-top: 20px;">
                <label>Sinopsis *</label>
                <textarea id="sinopsis" rows="4" placeholder="Escribe un breve resumen de la película..." required></textarea>
            </div>

            <div class="form-section-title">Géneros (Selecciona al menos uno)</div>
            <div class="genres-grid" id="generos-container">
                <label class="checkbox-label"><input type="checkbox" value="1" class="genero-cb"> Acción</label>
                <label class="checkbox-label"><input type="checkbox" value="2" class="genero-cb"> Aventura</label>
                <label class="checkbox-label"><input type="checkbox" value="3" class="genero-cb"> Ciencia Ficción</label>
                <label class="checkbox-label"><input type="checkbox" value="4" class="genero-cb"> Comedia</label>
                <label class="checkbox-label"><input type="checkbox" value="5" class="genero-cb"> Drama</label>
                <label class="checkbox-label"><input type="checkbox" value="6" class="genero-cb"> Fantasía</label>
                <label class="checkbox-label"><input type="checkbox" value="7" class="genero-cb"> Terror</label>
                <label class="checkbox-label"><input type="checkbox" value="8" class="genero-cb"> Musical</label>
                <label class="checkbox-label"><input type="checkbox" value="9" class="genero-cb"> Romance</label>
                <label class="checkbox-label"><input type="checkbox" value="10" class="genero-cb"> Suspenso</label>
                <label class="checkbox-label"><input type="checkbox" value="11" class="genero-cb"> Western</label>
            </div>

            <div class="form-section-title">Enlaces y Multimedia</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>URL del Tráiler (YouTube)</label>
                    <input type="url" id="url_trailer" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="form-group">
                    <label>URL del Video (MP4/HLS) *</label>
                    <input type="url" id="url_video" placeholder="https://tu-servidor.com/pelicula.mp4" required>
                </div>
            </div>

            <div class="media-upload-section">
                <div class="upload-box" onclick="document.getElementById('miniatura').click()">
                    <i class="fa fa-cloud-arrow-up"></i>
                    <p>Haz clic para subir la miniatura (Poster)</p>
                    <span id="file-name">Ningún archivo seleccionado</span>
                    <input type="file" id="miniatura" accept="image/*" style="display: none;" onchange="updateFileName(this)">
                </div>

                <div class="featured-toggle">
                    <label class="switch">
                        <input type="checkbox" id="destacada">
                        <span class="slider"></span>
                    </label>
                    <span>¿Película Destacada? (Aparecerá en el banner principal)</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="guardarPelicula()" id="btnGuardar">
                    <i class="fa fa-save"></i> Guardar Película
                </button>
            </div>

        </form>
    </div>
</main>

<script>
    // Tu JS necesita saber la URL base para redirigir si caduca el token
    const BASE_URL = "<?= base_url() ?>"; 
</script>
<script src="<?= base_url('assets/js/peliculas.js?v=' . time()) ?>"></script>