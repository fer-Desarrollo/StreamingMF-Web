<footer class="footer">
    © StreamingMF 2026
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function logout() {
    Swal.fire({
        title: "Cerrar sesión",
        text: "¿Seguro que deseas salir del panel?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#cc0000", /* Rojo StreamingMF */
        cancelButtonColor: "#6b7280", /* Gris elegante */
        confirmButtonText: "Sí, salir",
        cancelButtonText: "Cancelar"
    }).then(r => {
        if (r.isConfirmed) {
            localStorage.removeItem("token");
            window.location.href = "<?= base_url('auth/login') ?>";
        }
    });
}
</script>

</body>
</html>

</body>
</html>