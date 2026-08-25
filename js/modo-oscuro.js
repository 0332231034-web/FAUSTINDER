document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnModoOscuro');
    if (!btn) return;

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const activado = document.body.classList.toggle('tema-oscuro');
        document.cookie = "modo_oscuro=" + (activado ? "1" : "0") + ";path=/;max-age=" + (60*60*24*365);
        btn.textContent = activado ? '☀️' : '🌙';
    });
});