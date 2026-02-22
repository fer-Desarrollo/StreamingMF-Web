<link rel="stylesheet" href="<?= base_url('assets/css/catalogo.css?v=' . time()) ?>">

<main class="catalogo-main">
    <div class="catalogo-header">
        <h1>Catálogo de Películas</h1>
        <p>Explora nuestros títulos y disfruta del mejor contenido.</p>
    </div>

    <div class="movies-grid" id="moviesGrid">
        <div class="loading-movies"><i class="fa fa-spinner fa-spin"></i> Cargando catálogo...</div>
    </div>
</main>

<div id="vistaDetalle" class="detalle-overlay">
    <button class="btn-cerrar-detalle" onclick="cerrarDetalle()"><i class="fa fa-arrow-left"></i> Volver al Catálogo</button>
    
    <div class="detalle-content">
        <div class="detalle-poster">
            <img id="detalle_img" src="" alt="Póster">
        </div>
        
        <div class="detalle-info">
            <h1 id="detalle_titulo">Cargando...</h1>
            <div class="detalle-meta">
                <span id="detalle_anio" class="meta-item"></span>
                <span id="detalle_clasificacion" class="meta-badge"></span>
                <span id="detalle_duracion" class="meta-item"></span>
            </div>
            
            <p id="detalle_sinopsis" class="detalle-sinopsis"></p>
            
            <div class="detalle-extra">
                <p><strong>Título Original:</strong> <span id="detalle_original"></span></p>
                <p><strong>País:</strong> <span id="detalle_pais"></span></p>
                <p><strong>Idioma Original:</strong> <span id="detalle_idioma"></span></p>
            </div>

            <div class="detalle-acciones">
                <button id="btnReproducir" class="btn-play" onclick="abrirReproductor()">
                    <i class="fa fa-play"></i> Ver Tráiler
                </button>
            </div>
        </div>
    </div>
</div>

<div id="reproductorModal" class="reproductor-overlay">
    <button class="btn-cerrar-reproductor" onclick="cerrarReproductor()"><i class="fa fa-times"></i></button>
    <div class="video-container">
        <iframe id="youtubeFrame" src="" frameborder="0" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>
    </div>
</div>

<script>
    const BASE_URL = "<?= base_url() ?>";
</script>
<script src="<?= base_url('assets/js/catalogo.js?v=' . time()) ?>"></script>