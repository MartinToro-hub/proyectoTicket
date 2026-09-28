function togglePassword() {
    const input = document.getElementById('passwordInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function recuperarPassword(event) {
    event.preventDefault();
    const email = prompt("Ingresa tu correo institucional para verificar la solicitud:");
    if (!email) return;

    if (email.includes("@") && email.includes(".")) {
        alert("Solicitud recibida. Si el correo existe en el sistema, se enviarán las instrucciones.");
    } else {
        alert("Por favor ingresa un correo electrónico válido.");
    }
}

function abrirModal() {
    const modal = document.getElementById('customAlert');
    if (modal) modal.classList.add('show');
}

function cerrarModal() {
    const modal = document.getElementById('customAlert');
    if (modal) modal.classList.remove('show');
    // Quita ?error=1 de la URL
    window.history.replaceState({}, document.title, window.location.pathname);
}

// Este script se carga al final del <body>, por lo que el DOM ya existe.
if (new URLSearchParams(window.location.search).get('error') === '1') {
    abrirModal();
}