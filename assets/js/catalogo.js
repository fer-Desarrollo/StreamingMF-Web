/* =========================================
   LÓGICA: CATÁLOGO Y REPRODUCTOR
   ========================================= */

const API_URL = 'https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api';
let urlTrailerActual = '';

document.addEventListener('DOMContentLoaded', () => {
    cargarCatalogo();
});

// 1. CARGAR CUADRÍCULA DE PELÍCULAS
async function cargarCatalogo() {
    const token = localStorage.getItem('token');
    const grid = document.getElementById('moviesGrid');

    try {
        const response = await fetch(`${API_URL}/peliculas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (response.ok) {
            grid.innerHTML = ''; 

            if (data.length === 0) {
                grid.innerHTML = '<div class="loading-movies">No hay películas disponibles en este momento.</div>';
                return;
            }

            data.forEach(pelicula => {
                const imgId = `poster-cat-${pelicula.id_pelicula}`;
                
                const card = document.createElement('div');
                card.className = 'movie-card';
                card.onclick = () => cargarDetalle(pelicula.id_pelicula, pelicula.miniatura_url);
                
                card.innerHTML = `
                    <img id="${imgId}" src="https://via.placeholder.com/200x300?text=Cargando..." alt="${pelicula.titulo}">
                `;
                grid.appendChild(card);

                // Descargar imagen segura
                if(pelicula.miniatura_url) {
                    cargarImagenSegura(pelicula.miniatura_url, imgId, token);
                }
            });
        }
    } catch (error) {
        grid.innerHTML = '<div class="loading-movies text-red">Error al cargar el catálogo.</div>';
    }
}

// 2. CARGAR DETALLE DE LA PELÍCULA (SEGUNDA VENTANA)
async function cargarDetalle(id, miniatura_url_grid) {
    const token = localStorage.getItem('token');
    
    // Mostramos la ventana de detalle primero para dar sensación de rapidez
    document.getElementById('vistaDetalle').classList.add('active');
    
    // Ponemos datos temporales de carga
    document.getElementById('detalle_titulo').innerText = "Cargando...";
    document.getElementById('detalle_sinopsis').innerText = "";
    const imgDetalle = document.getElementById('detalle_img');
    imgDetalle.src = "https://via.placeholder.com/400x600?text=Cargando...";

    try {
        // Consultar el endpoint de detalle
        const response = await fetch(`${API_URL}/peliculas/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (response.ok) {
            document.getElementById('detalle_titulo').innerText = data.titulo;
            document.getElementById('detalle_anio').innerText = data.anio_estreno;
            document.getElementById('detalle_clasificacion').innerText = data.clasificacion;
            document.getElementById('detalle_duracion').innerText = `${data.duracion_min} min`;
            document.getElementById('detalle_sinopsis').innerText = data.sinopsis;
            document.getElementById('detalle_original').innerText = data.titulo_original || 'N/A';
            document.getElementById('detalle_pais').innerText = data.pais_produccion || 'N/A';
            document.getElementById('detalle_idioma').innerText = data.idioma_original || 'N/A';

            // Guardamos la URL del trailer para cuando le den a "Play"
            urlTrailerActual = data.url_trailer || data.url_video;

            // Descargar la imagen segura para el detalle
            if (data.miniatura_url || miniatura_url_grid) {
                cargarImagenSegura(data.miniatura_url || miniatura_url_grid, 'detalle_img', token);
            }
        }
    } catch (error) {
        console.error("Error al cargar detalle", error);
    }
}

function cerrarDetalle() {
    document.getElementById('vistaDetalle').classList.remove('active');
}

// 3. REPRODUCIR EL TRÁILER
function abrirReproductor() {
    if (!urlTrailerActual) {
        alert("Esta película no tiene un tráiler disponible.");
        return;
    }

    const iframe = document.getElementById('youtubeFrame');
    
    // CONVERTIR URL DE YOUTUBE A FORMATO EMBED
    let embedUrl = '';
    if (urlTrailerActual.includes('youtube.com/watch')) {
        const urlParams = new URL(urlTrailerActual).searchParams;
        embedUrl = `https://www.youtube.com/embed/${urlParams.get('v')}?autoplay=1`;
    } else if (urlTrailerActual.includes('youtu.be/')) {
        const videoId = urlTrailerActual.split('youtu.be/')[1].split('?')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    } else {
        // Por si acaso es otro tipo de link, intentamos ponerlo directo
        embedUrl = urlTrailerActual;
    }

    iframe.src = embedUrl;
    document.getElementById('reproductorModal').classList.add('active');
}

function cerrarReproductor() {
    document.getElementById('reproductorModal').classList.remove('active');
    document.getElementById('youtubeFrame').src = ""; // Detener el video
}

// FUNCIÓN REUTILIZABLE PARA IMÁGENES CON TOKEN
async function cargarImagenSegura(url, imgId, token) {
    try {
        const response = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });
        if (response.ok) {
            const blob = await response.blob();
            document.getElementById(imgId).src = URL.createObjectURL(blob);
        }
    } catch (error) {
        console.error("Error de imagen", error);
    }
}