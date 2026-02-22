/* =========================================
   LÓGICA: CONSULTA Y EDICIÓN DE PELÍCULAS
   ========================================= */

const API_URL = 'https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api';

document.addEventListener('DOMContentLoaded', () => {
    cargarPeliculas();

    // Evento para guardar la edición
    document.getElementById('formEditarPelicula').addEventListener('submit', guardarEdicion);
});

// 1. CARGAR LA TABLA
async function cargarPeliculas() {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tbodyPeliculas');

    try {
        const response = await fetch(`${API_URL}/peliculas/admin`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (response.ok) {
            tbody.innerHTML = ''; 
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay películas registradas.</td></tr>';
                return;
            }

            data.forEach(pelicula => {
                const esActiva = pelicula.activa == 1;
                const badgeEstado = esActiva ? '<span class="badge badge-active">Activo</span>' : '<span class="badge badge-inactive">Inactivo</span>';
                const btnToggleIcon = esActiva ? 'fa-toggle-on' : 'fa-toggle-off';
                const btnToggleClass = esActiva ? 'btn-toggle-off' : 'btn-toggle-on'; 
                const toggleTitle = esActiva ? 'Desactivar Película' : 'Activar Película';
                
                // Creamos un ID único para inyectar la imagen después
                const imgId = `poster-${pelicula.id_pelicula}`;

                const tr = document.createElement('tr');
                
                // IMPORTANTE: El src de la imagen ahora es un placeholder, NO la URL de tu API.
                // Así evitamos el error 401 automático del navegador.
                tr.innerHTML = `
                    <td>
                        <img id="${imgId}" src="https://via.placeholder.com/60x85?text=Cargando..." class="movie-thumbnail" alt="Póster">
                    </td>
                    <td><strong>${pelicula.titulo}</strong></td>
                    <td>${pelicula.anio_estreno}</td>
                    <td>${pelicula.clasificacion}</td>
                    <td>${badgeEstado}</td>
                    <td class="text-center">
                        <button class="action-btn btn-edit" title="Editar" onclick="abrirModalEditar(${pelicula.id_pelicula})">
                            <i class="fa fa-pen"></i>
                        </button>
                        <button class="action-btn ${btnToggleClass}" title="${toggleTitle}" onclick="cambiarEstado(${pelicula.id_pelicula}, ${pelicula.activa})">
                            <i class="fa ${btnToggleIcon}"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);

                // Disparamos la función segura que sí envía el token para descargar la imagen
                if(pelicula.miniatura_url) {
                    cargarImagenSegura(pelicula.miniatura_url, imgId, token);
                }
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center" style="color:red;">Error: ${data.message}</td></tr>`;
        }
    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error de conexión al cargar la lista.</td></tr>';
    }
}

// 1.5 FUNCIÓN PARA DESCARGAR LA IMAGEN ENVIANDO EL TOKEN
async function cargarImagenSegura(url, imgId, token) {
    try {
        const response = await fetch(url, {
            headers: { 'Authorization': `Bearer ${token}` } // Aquí sí enviamos el token
        });
        
        if (response.ok) {
            // Convertimos la respuesta segura en un archivo visible para el navegador
            const blob = await response.blob();
            const objectURL = URL.createObjectURL(blob);
            document.getElementById(imgId).src = objectURL;
        } else {
            document.getElementById(imgId).src = "https://via.placeholder.com/60x85?text=Sin+Foto";
        }
    } catch (error) {
        document.getElementById(imgId).src = "https://via.placeholder.com/60x85?text=Error";
    }
}
// 2. ABRIR MODAL (Y OBTENER DATOS DE LA PELÍCULA)
async function abrirModalEditar(id) {
    const token = localStorage.getItem('token');
    
    // Obtenemos los detalles de la película llamando a tu API
    // (Asume que existe un endpoint GET /peliculas/{id})
    try {
        const response = await fetch(`${API_URL}/peliculas/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (response.ok) {
            // Llenar los inputs del modal con los datos traídos
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_titulo').value = data.titulo || '';
            document.getElementById('edit_titulo_original').value = data.titulo_original || '';
            document.getElementById('edit_anio').value = data.anio_estreno || '';
            document.getElementById('edit_duracion').value = data.duracion_min || '';
            document.getElementById('edit_clasificacion').value = data.clasificacion || '';
            document.getElementById('edit_idioma').value = data.idioma_original || '';
            document.getElementById('edit_pais').value = data.pais_produccion || '';
            document.getElementById('edit_trailer').value = data.url_trailer || '';
            document.getElementById('edit_sinopsis').value = data.sinopsis || '';
            document.getElementById('edit_video').value = data.url_video || '';

            // Mostrar el Modal con animación
            document.getElementById('modalEditar').classList.add('active');
        } else {
            Swal.fire("Error", "No se pudieron obtener los detalles de la película.", "error");
        }
    } catch (error) {
        console.error("Error al traer película:", error);
        Swal.fire("Error", "Ocurrió un error al cargar los datos.", "error");
    }
}

// CERRAR MODAL
function cerrarModal() {
    document.getElementById('modalEditar').classList.remove('active');
    document.getElementById('formEditarPelicula').reset(); // Limpia los campos
}

// 3. GUARDAR LOS CAMBIOS (PUT)
async function guardarEdicion(e) {
    e.preventDefault(); // Evita que la página recargue
    const token = localStorage.getItem('token');
    const id = document.getElementById('edit_id').value;
    
    // Armar el payload JSON esperado por tu API
    const payload = {
        titulo: document.getElementById('edit_titulo').value.trim(),
        titulo_original: document.getElementById('edit_titulo_original').value.trim(),
        sinopsis: document.getElementById('edit_sinopsis').value.trim(),
        anio_estreno: parseInt(document.getElementById('edit_anio').value),
        duracion_min: parseInt(document.getElementById('edit_duracion').value),
        clasificacion: document.getElementById('edit_clasificacion').value,
        idioma_original: document.getElementById('edit_idioma').value.trim(),
        pais_produccion: document.getElementById('edit_pais').value.trim(),
        url_trailer: document.getElementById('edit_trailer').value.trim(),
        url_video: document.getElementById('edit_video').value.trim()
    };

    const btn = document.getElementById('btnActualizar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    try {
        const response = await fetch(`${API_URL}/peliculas/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();

        if (response.ok) {
            Swal.fire("¡Actualizada!", "La película se actualizó correctamente.", "success");
            cerrarModal();
            cargarPeliculas(); // Refrescar la tabla para ver cambios
        } else {
            Swal.fire("Error", data.message || "No se pudo actualizar.", "error");
        }
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "Error de conexión con el servidor.", "error");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
    }
}

// 4. ACTIVAR / DESACTIVAR (POST ESTADO)
// 4. ACTIVAR / DESACTIVAR (PATCH ESTADO)
async function cambiarEstado(id, estadoActual) {
    const token = localStorage.getItem('token');
    
    // Si es 1 (activo) lo pasamos a 0. Si es 0, lo pasamos a 1.
    const nuevoEstado = estadoActual == 1 ? 0 : 1;
    const accionTexto = nuevoEstado === 1 ? 'activar' : 'desactivar';

    Swal.fire({
        title: `¿Deseas ${accionTexto} esta película?`,
        text: nuevoEstado === 0 ? "La película ya no estará visible para los usuarios." : "La película volverá a ser visible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#cc0000',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // AQUÍ ESTÁ EL CAMBIO CLAVE: method: 'PATCH'
                const response = await fetch(`${API_URL}/peliculas/${id}/estado`, {
                    method: 'PATCH', 
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ activa: nuevoEstado })
                });
                
                if (response.ok) {
                    Swal.fire({
                        title: "¡Listo!", 
                        text: `La película ha sido ${accionTexto === 'activar' ? 'activada' : 'desactivada'}.`, 
                        icon: "success",
                        confirmButtonColor: "#cc0000"
                    });
                    cargarPeliculas(); // Refrescar tabla automáticamente para ver el cambio de color
                } else {
                    const data = await response.json();
                    Swal.fire("Error", data.message || "No se pudo cambiar el estado.", "error");
                }
            } catch (error) {
                console.error("Error en la petición PATCH:", error);
                Swal.fire("Error", "Error de conexión con el servidor.", "error");
            }
        }
    });
}