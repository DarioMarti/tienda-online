// Gestión de Tabs
function switchTab(tabId) {
    // Ocultar todas las secciones
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });

    // Mostrar la sección seleccionada
    const selectedSection = document.getElementById(`${tabId}-section`);
    if (selectedSection) {
        selectedSection.classList.remove('hidden');
        selectedSection.classList.add('block');
    }

    // Actualizar estado activo en el sidebar
    document.querySelectorAll('.nav-item').forEach(el => {
        el.classList.remove('bg-fashion-gray', 'text-fashion-black');
        el.classList.add('text-gray-500', 'hover:bg-fashion-gray', 'hover:text-fashion-black');
    });

    const activeBtn = document.querySelector(`button[data-tab="${tabId}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('text-gray-500', 'hover:bg-fashion-gray', 'hover:text-fashion-black');
        activeBtn.classList.add('bg-fashion-gray', 'text-fashion-black');
    }
}

// Gestión del Modal de Usuarios
const userModal = document.getElementById('user-modal');
const userForm = document.getElementById('user-form');
const modalTitle = document.getElementById('modal-title');
const formAction = document.getElementById('form_action');
const userIdInput = document.getElementById('user_id');
const passwordGroup = document.getElementById('password-group');
const passwordHint = document.getElementById('password-hint');
const rolSelect = document.getElementById('rol');

function openUserModal() {
    userModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevenir scroll

    // Resetear formulario para "Crear"
    userForm.reset();
    modalTitle.textContent = 'Nuevo Usuario';
    formAction.value = 'create';
    userIdInput.value = '';
    passwordGroup.style.display = 'block'; // Mostrar campo contraseña
    passwordHint.style.display = 'none'; // Ocultar pista de "dejar en blanco"

    // Habilitar selector de rol por defecto
    rolSelect.disabled = false;
    // Eliminar input hidden de rol si existe
    const hiddenRol = userForm.querySelector('input[name="rol"][type="hidden"]');
    if (hiddenRol) hiddenRol.remove();
}

function closeUserModal() {
    userModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editUser(user) {
    openUserModal();

    modalTitle.textContent = 'Editar Usuario';
    formAction.value = 'update';
    userIdInput.value = user.id;

    // Rellenar datos
    document.getElementById('nombre').value = user.nombre;
    document.getElementById('apellidos').value = user.apellidos;
    document.getElementById('email').value = user.email;
    document.getElementById('rol').value = user.rol;
    document.getElementById('activo').value = user.activo;

    // Ajustes para edición
    passwordHint.style.display = 'block';

    // Protección: Si el usuario intenta editarse a sí mismo
    if (user.id == currentUserId) {
        rolSelect.disabled = true; // Deshabilitar cambio de rol

        // Añadir input hidden para enviar el valor del rol (ya que disabled no se envía)
        let hiddenRol = userForm.querySelector('input[name="rol"][type="hidden"]');
        if (!hiddenRol) {
            hiddenRol = document.createElement('input');
            hiddenRol.type = 'hidden';
            hiddenRol.name = 'rol';
            userForm.appendChild(hiddenRol);
        }
        hiddenRol.value = user.rol;
    } else {
        rolSelect.disabled = false;
        const hiddenRol = userForm.querySelector('input[name="rol"][type="hidden"]');
        if (hiddenRol) hiddenRol.remove();
    }
}

// Cerrar modal al hacer clic fuera
userModal.addEventListener('click', (e) => {
    if (e.target === userModal) {
        closeUserModal();
    }
});

// Placeholder functions for new buttons
function openOrderModal() {
    alert('Funcionalidad de Nuevo Pedido próximamente.');
}

function openProductModal() {
    alert('Funcionalidad de Nuevo Producto próximamente.');
}

function openCategoryModal() {
    alert('Funcionalidad de Nueva Categoría próximamente.');
}
