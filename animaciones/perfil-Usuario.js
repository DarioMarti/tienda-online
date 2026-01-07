
// GESTIÓN DE MODALES (UNIFICADA)
function abrirModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// CERRAR MODALES AL HACER CLIC FUERA O CON ESC
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
        cerrarModal(e.target.id);
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const modalAbierto = document.querySelector('div[id$="modal"]:not(.hidden)');
        if (modalAbierto) cerrarModal(modalAbierto.id);
    }
});

// --- PERFIL: EDICIÓN ---
const btnEditar = document.getElementById('edit-profile-btn');
const formEditar = document.getElementById('edit-profile-form');

if (btnEditar) {
    btnEditar.addEventListener('click', () => abrirModal('edit-modal'));
}

document.getElementById('close-modal')?.addEventListener('click', () => cerrarModal('edit-modal'));
document.getElementById('cancel-btn')?.addEventListener('click', () => cerrarModal('edit-modal'));

if (formEditar) {
    formEditar.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(formEditar);

        try {
            const res = await fetch('../modelos/usuarios/modificar-usuario.php', {
                method: 'POST',
                body: fd
            });

            const result = await res.json();
            cerrarModal('edit-modal');

            if (result.success) {
                showResultModal('success', result.message);
                updateProfileUI(result.data);
            } else {
                showResultModal('error', result.message);
            }
        } catch (err) {
            cerrarModal('edit-modal');
            showResultModal('error', 'Error de conexión');
        }
    });
}

// --- PERFIL: CONTRASEÑA ---
const btnPass = document.getElementById('btnCambiarContrasena');
const formPass = document.getElementById('change-password-form');

if (btnPass) {
    btnPass.addEventListener('click', () => {
        abrirModal('change-password-modal');
        formPass?.reset();
    });
}

document.getElementById('close-password-modal')?.addEventListener('click', () => cerrarModal('change-password-modal'));
document.getElementById('cancel-password-btn')?.addEventListener('click', () => cerrarModal('change-password-modal'));

if (formPass) {
    formPass.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(formPass);

        if (fd.get('new_password') !== fd.get('confirm_password')) {
            showResultModal('error', 'Las contraseñas no coinciden');
            return;
        }

        try {
            const res = await fetch('../modelos/usuarios/cambiar-contrasena.php', {
                method: 'POST',
                body: fd
            });
            const result = await res.json();

            cerrarModal('change-password-modal');
            showResultModal(result.success ? 'success' : 'error', result.message);
        } catch (err) {
            cerrarModal('change-password-modal');
            showResultModal('error', 'Error de conexión');
        }
    });
}

// --- PERFIL: ELIMINAR CUENTA ---
document.getElementById('eliminar-cuenta')?.addEventListener('click', (e) => {
    e.preventDefault();
    abrirModal('delete-account-modal');
});

document.getElementById('cancel-delete-btn')?.addEventListener('click', () => cerrarModal('delete-account-modal'));
document.getElementById('confirm-delete-btn')?.addEventListener('click', () => {
    window.location.href = '../modelos/usuarios/eliminar-usuario.php';
});

// --- MODAL DE RESULTADOS ---
function showResultModal(type, msg) {
    const icon = document.getElementById('result-icon');
    const title = document.getElementById('result-title');
    const message = document.getElementById('result-message');

    if (!icon || !title || !message) return;

    if (type === 'success') {
        icon.innerHTML = '<i class="ph ph-check-circle text-6xl text-green-500"></i>';
        title.textContent = '¡Éxito!';
    } else {
        icon.innerHTML = '<i class="ph ph-x-circle text-6xl text-red-500"></i>';
        title.textContent = 'Error';
    }

    message.textContent = msg;
    abrirModal('result-modal');
}

document.getElementById('close-result-modal')?.addEventListener('click', () => cerrarModal('result-modal'));

// --- ACTUALIZAR INTERFAZ ---
function updateProfileUI(data) {
    // Títulos y Nombre de usuario
    const headerTitle = document.querySelector('h1.font-editorial');
    if (headerTitle) headerTitle.textContent = `${data.nombre} ${data.apellidos}`;

    const navUser = document.querySelector('.ph-user-circle + span');
    if (navUser) navUser.textContent = data.nombre;

    // Campos de visualización (IDs únicos añadidos en el HTML)
    const fields = {
        'display-nombre': data.nombre,
        'display-apellidos': data.apellidos,
        'display-telefono': data.telefono || 'No especificado',
        'display-direccion': data.direccion || 'No especificada'
    };

    for (const [id, value] of Object.entries(fields)) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }
}
