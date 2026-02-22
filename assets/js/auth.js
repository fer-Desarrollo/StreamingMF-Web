function login() {
    fetch("https://mediumvioletred-kudu-220345.hostingersite.com/Streaming/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            email: email.value,
            password: password.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.token) {
            localStorage.setItem("token", data.token);

            Swal.fire({
                icon: "success",
                title: "Acceso correcto",
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = BASE_URL + "dashboard";
            });

        } else {
            Swal.fire("Error", "Credenciales incorrectas", "error");
        }
    })
    .catch(() => {
        Swal.fire("Error", "No se pudo conectar al servidor", "error");
    });
}