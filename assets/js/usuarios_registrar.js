/* =========================================
   LÓGICA: REGISTRAR USUARIOS (CLIENTES)
   ========================================= */

const API_URL = 'https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api';

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formRegistrarUsuario').addEventListener('submit', registrarUsuario);
});

async function registrarUsuario(e) {
    e.preventDefault(); // Evitamos que la página se recargue al enviar el formulario

    const token = localStorage.getItem('token');
    if (!token) {
        Swal.fire("Sesión expirada", "Por favor inicia sesión nuevamente.", "warning").then(() => {
            window.location.href = BASE_URL + "auth/login";
        });
        return;
    }

    // 1. Obtener valores de los inputs
    const nombres = document.getElementById('nombres').value.trim();
    const apPaterno = document.getElementById('apellido_paterno').value.trim();
    const apMaterno = document.getElementById('apellido_materno').value.trim();
    
    // Concatenar apellidos (si no hay materno, solo manda el paterno)
    const apellidos = apMaterno ? `${apPaterno} ${apMaterno}` : apPaterno;

    const email = document.getElementById('email').value.trim();
    const nombre_usuario = document.getElementById('nombre_usuario').value.trim();

    // 2. Armar el objeto exacto que pide tu API
    const payload = {
        nombres: nombres,
        apellidos: apellidos,
        email: email,
        nombre_usuario: nombre_usuario
    };

    const btn = document.getElementById('btnGuardarUsuario');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando correo...';

    try {
        const response = await fetch(`${API_URL}/usuarios`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok) {
            Swal.fire({
                title: "¡Usuario Registrado!",
                text: "Se ha enviado un correo electrónico con las credenciales al nuevo usuario.",
                icon: "success",
                confirmButtonColor: "#cc0000"
            }).then(() => {
                document.getElementById('formRegistrarUsuario').reset(); // Limpia el formulario
            });
        } else {
            Swal.fire("Error", data.message || "No se pudo registrar al usuario.", "error");
        }
    } catch (error) {
        console.error("Error al registrar usuario:", error);
        Swal.fire("Error", "Ocurrió un problema de conexión con el servidor.", "error");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane"></i> Registrar y Enviar Correo';
    }
}