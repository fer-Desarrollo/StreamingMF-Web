/* =========================================
   LÓGICA: CONSULTA Y EDICIÓN DE USUARIOS
   ========================================= */

const API_URL = 'https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api';
let usuariosData = []; // Guardamos los usuarios aquí para llenar el modal rápido

document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
    // Evento para el submit del formulario de edición
    document.getElementById('formEditarUsuario').addEventListener('submit', guardarEdicionUsuario);
});

// 1. CARGAR LA TABLA (GET)
async function cargarUsuarios() {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tbodyUsuarios');

    try {
        const response = await fetch(`${API_URL}/usuarios/admin`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (response.ok) {
            usuariosData = data; // Guardamos la data
            tbody.innerHTML = ''; 
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay usuarios registrados.</td></tr>';
                return;
            }

            data.forEach(user => {
                const esActivo = user.activo == 1;
                const badgeEstado = esActivo ? '<span class="badge badge-active">Activo</span>' : '<span class="badge badge-inactive">Inactivo</span>';
                
                // Botones y colores según estado
                const btnToggleIcon = esActivo ? 'fa-toggle-on' : 'fa-toggle-off';
                const btnToggleClass = esActivo ? 'btn-toggle-off' : 'btn-toggle-on'; 
                const toggleTitle = esActivo ? 'Desactivar Usuario' : 'Activar Usuario';

                // Badge de Rol
                const badgeRol = user.rol.toLowerCase() === 'admin' 
                    ? '<span class="badge badge-rol-admin"><i class="fa fa-shield-halved"></i> Admin</span>' 
                    : '<span class="badge badge-rol-user"><i class="fa fa-user"></i> Suscriptor</span>';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>@${user.nombre_usuario}</strong></td>
                    <td>${user.nombres} ${user.apellidos}</td>
                    <td>${user.email}</td>
                    <td>${badgeRol}</td>
                    <td>${badgeEstado}</td>
                    <td class="text-center">
                        <button class="action-btn btn-edit" title="Editar" onclick="abrirModalEditar(${user.id_usuario})">
                            <i class="fa fa-pen"></i>
                        </button>
                        <button class="action-btn ${btnToggleClass}" title="${toggleTitle}" onclick="cambiarEstadoUsuario(${user.id_usuario}, ${user.activo})">
                            <i class="fa ${btnToggleIcon}"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center" style="color:red;">Error: ${data.message}</td></tr>`;
        }
    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error de conexión al cargar usuarios.</td></tr>';
    }
}

// 2. ABRIR MODAL
function abrirModalEditar(id) {
    // Buscar el usuario en nuestro array guardado
    const user = usuariosData.find(u => u.id_usuario == id);
    if(!user) return;

    // Llenar inputs
    document.getElementById('edit_id_usuario').value = user.id_usuario;
    document.getElementById('edit_activo').value = user.activo; // Mantenemos su estado actual
    document.getElementById('edit_nombres').value = user.nombres || '';
    document.getElementById('edit_apellidos').value = user.apellidos || '';
    document.getElementById('edit_telefono').value = user.telefono || '';
    document.getElementById('edit_pais').value = user.pais || '';
    document.getElementById('edit_ciudad').value = user.ciudad || '';
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_nombre_usuario').value = user.nombre_usuario || '';

    // Seleccionar el rol correcto (Ajusta esto según los IDs de roles de tu BD, ej: admin = 1, suscriptor = 3)
    let rolId = user.rol.toLowerCase() === 'admin' ? "1" : "3";
    document.getElementById('edit_id_rol').value = rolId;

    // Mostrar modal
    document.getElementById('modalEditarUsuario').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalEditarUsuario').classList.remove('active');
    document.getElementById('formEditarUsuario').reset();
}

// 3. ACTUALIZAR USUARIO (PUT)
async function guardarEdicionUsuario(e) {
    e.preventDefault();
    const token = localStorage.getItem('token');
    const id = document.getElementById('edit_id_usuario').value;
    
    // Armar payload exacto para el PUT
    const payload = {
        email: document.getElementById('edit_email').value.trim(),
        nombre_usuario: document.getElementById('edit_nombre_usuario').value.trim(),
        id_rol: parseInt(document.getElementById('edit_id_rol').value),
        activo: parseInt(document.getElementById('edit_activo').value),
        nombres: document.getElementById('edit_nombres').value.trim(),
        apellidos: document.getElementById('edit_apellidos').value.trim(),
        telefono: document.getElementById('edit_telefono').value.trim(),
        pais: document.getElementById('edit_pais').value.trim(),
        ciudad: document.getElementById('edit_ciudad').value.trim()
    };

    const btn = document.getElementById('btnActualizarUsuario');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    try {
        const response = await fetch(`${API_URL}/usuarios/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();

        if (response.ok) {
            Swal.fire("¡Actualizado!", "La información del usuario se guardó correctamente.", "success");
            cerrarModal();
            cargarUsuarios(); // Refrescar tabla
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

// 4. ACTIVAR / DESACTIVAR (PATCH)
async function cambiarEstadoUsuario(id, estadoActual) {
    const token = localStorage.getItem('token');
    const nuevoEstado = estadoActual == 1 ? 0 : 1;
    const accionTexto = nuevoEstado === 1 ? 'activar' : 'desactivar';

    Swal.fire({
        title: `¿Deseas ${accionTexto} a este usuario?`,
        text: nuevoEstado === 0 ? "No podrá iniciar sesión en la plataforma." : "Tendrá acceso nuevamente al sistema.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#cc0000',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`${API_URL}/usuarios/${id}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ activo: nuevoEstado })
                });
                
                if (response.ok) {
                    Swal.fire("¡Listo!", `El usuario ha sido ${accionTexto === 'activar' ? 'activado' : 'desactivado'}.`, "success");
                    cargarUsuarios(); // Refrescar tabla
                } else {
                    const data = await response.json();
                    Swal.fire("Error", data.message || "No se pudo cambiar el estado.", "error");
                }
            } catch (error) {
                console.error(error);
                Swal.fire("Error", "Error de conexión.", "error");
            }
        }
    });
}