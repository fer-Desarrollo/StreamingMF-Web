/* =========================================
   LÓGICA ESPECÍFICA: REGISTRAR PELÍCULAS
   Ubicación: assets/js/peliculas.js
   ========================================= */

const BASE_API_URL = 'https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api';

// Mostrar nombre del archivo al seleccionarlo en la miniatura
function updateFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : "Ningún archivo seleccionado";
    document.getElementById('file-name').innerText = fileName;
}

// Función principal para guardar
async function guardarPelicula() {
    const token = localStorage.getItem('token');
    
    // Validar sesión
    if (!token) {
        Swal.fire("Sesión expirada", "Por favor inicia sesión nuevamente.", "warning").then(() => {
            window.location.href = BASE_URL + "auth/login"; 
        });
        return;
    }

    // 1. Recolectar géneros seleccionados (Array de enteros)
    const generosCb = document.querySelectorAll('.genero-cb:checked');
    const generosSeleccionados = Array.from(generosCb).map(cb => parseInt(cb.value));
    
    if (generosSeleccionados.length === 0) {
        Swal.fire("Atención", "Debes seleccionar al menos un género", "warning");
        return;
    }

    // 2. Armar el JSON exacto para el POST /peliculas
    const payload = {
        titulo: document.getElementById('titulo').value.trim(),
        titulo_original: document.getElementById('titulo_original').value.trim(),
        sinopsis: document.getElementById('sinopsis').value.trim(),
        anio_estreno: parseInt(document.getElementById('anio_estreno').value),
        duracion_min: parseInt(document.getElementById('duracion_min').value),
        clasificacion: document.getElementById('clasificacion').value,
        idioma_original: document.getElementById('idioma_original').value.trim(),
        pais_produccion: document.getElementById('pais_produccion').value.trim(),
        url_trailer: document.getElementById('url_trailer').value.trim(),
        url_video: document.getElementById('url_video').value.trim(),
        destacada: document.getElementById('destacada').checked ? 1 : 0,
        generos: generosSeleccionados
    };

    // Validaciones básicas de campos vacíos
    if (!payload.titulo || !payload.anio_estreno || !payload.duracion_min || !payload.url_video || !payload.sinopsis || !payload.clasificacion) {
        Swal.fire("Campos incompletos", "Por favor llena todos los campos obligatorios (*)", "warning");
        return;
    }

    const btnGuardar = document.getElementById('btnGuardar');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    try {
        // ==========================================
        // PASO 1: CREAR LA PELÍCULA
        // ==========================================
        const response = await fetch(`${BASE_API_URL}/peliculas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            Swal.fire("Error al registrar", data.message || "No se pudo registrar la película", "error");
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Película';
            return;
        }

        // Buscamos el ID en las diferentes formas en que tu API podría devolverlo
        let peliculaId = data.id_pelicula || data.id || data.insert_id;
        if (!peliculaId && data.data) {
            peliculaId = data.data.id_pelicula || data.data.id;
        }

        // Si la película se creó, pero no encontramos el ID para subir la foto
        if (!peliculaId) {
            console.error("Respuesta del API sin ID claro:", data);
            Swal.fire("Película Guardada", "Se guardaron los datos, pero el servidor no devolvió el ID para subir la imagen. Revisa la base de datos.", "warning");
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Película';
            return;
        }

        // ==========================================
        // PASO 2: SUBIR LA MINIATURA AL ENDPOINT
        // ==========================================
        const miniaturaInput = document.getElementById('miniatura');
        
        if (miniaturaInput.files.length > 0) {
            const formData = new FormData();
            
            // Asignamos el archivo al nombre 'miniatura'. 
            // Si en PHP usas $_FILES['imagen'], cambia 'miniatura' por 'imagen' aquí.
            formData.append('miniatura', miniaturaInput.files[0]);

            try {
                const imgResponse = await fetch(`${BASE_API_URL}/peliculas/${peliculaId}/miniatura`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                        // MUY IMPORTANTE: ¡NO pongas 'Content-Type' aquí! 
                        // El navegador detecta el FormData y pone el 'multipart/form-data' automático.
                    },
                    body: formData
                });

                if (!imgResponse.ok) {
                    const imgData = await imgResponse.json().catch(() => ({}));
                    console.error("Error del servidor al subir miniatura:", imgData);
                    Swal.fire("Película creada", "Los datos se guardaron, pero la imagen fue rechazada por el servidor: " + (imgData.message || "Error desconocido"), "warning");
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Película';
                    return;
                }
            } catch (errorFila) {
                console.error("Error de red al subir imagen:", errorFila);
                Swal.fire("Película creada", "Hubo un error de conexión al intentar subir la imagen.", "warning");
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Película';
                return;
            }
        }

        // ==========================================
        // PASO 3: ÉXITO TOTAL
        // ==========================================
        Swal.fire({
            title: "¡Éxito!",
            text: "La película y su miniatura se registraron correctamente en el catálogo.",
            icon: "success",
            confirmButtonColor: "#cc0000"
        }).then(() => {
            document.getElementById('formPelicula').reset();
            document.getElementById('file-name').innerText = "Ningún archivo seleccionado";
            // window.location.href = BASE_URL + "peliculas"; // Opcional para redirigir
        });

    } catch (error) {
        console.error("Error general en la petición:", error);
        Swal.fire("Error", "Ocurrió un problema crítico de conexión con el servidor.", "error");
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Película';
    }
}