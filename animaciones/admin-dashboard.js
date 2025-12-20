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

function deleteUser(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar al usuario "${nombre}"? Esta acción no se puede deshacer.`)) {
        window.location.href = `../modelos/usuarios/admin-eliminar-usuario.php?id=${id}`;
    }
}

// Placeholder functions for new buttons
function openOrderModal() {
    alert('Funcionalidad de Nuevo Pedido próximamente.');
}

function openProductModal() {
    alert('Funcionalidad de Nuevo Producto próximamente.');
}

// Gestión del Modal de Categorías
const categoryModal = document.getElementById('category-modal');
const categoryForm = document.getElementById('category-form');
const categoryModalTitle = document.getElementById('category-modal-title');
const categoryFormAction = document.getElementById('category_form_action');
const categoryIdInput = document.getElementById('category_id');

function openCategoryModal() {
    categoryModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Resetear formulario
    categoryForm.reset();
    categoryModalTitle.textContent = 'Nueva Categoría';
    categoryFormAction.value = 'create';
    categoryIdInput.value = '';
}

function closeCategoryModal() {
    categoryModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editCategory(category) {
    openCategoryModal();

    // Cambiar título y acción
    categoryModalTitle.textContent = 'Editar Categoría';
    categoryFormAction.value = 'update';
    categoryIdInput.value = category.id;

    // Rellenar datos
    document.getElementById('cat_nombre').value = category.nombre;
    document.getElementById('cat_descripcion').value = category.descripcion;
    document.getElementById('cat_parent_id').value = category.categoria_padre_id || '';
}

function deleteCategory(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar la categoría "${nombre}"? Esta acción no se puede deshacer.`)) {
        window.location.href = `../modelos/categorias/eliminar-categoria.php?id=${id}`;
    }
}

// Cerrar modal al hacer clic fuera
if (categoryModal) {
    categoryModal.addEventListener('click', (e) => {
        if (e.target === categoryModal) {
            closeCategoryModal();
        }
    });
}

// Gestión del Modal de Notificaciones
const notificationModal = document.getElementById('notification-modal');
const notificationIcon = document.getElementById('notification-icon');
const notificationTitle = document.getElementById('notification-title');
const notificationMessage = document.getElementById('notification-message');

function showNotification(type, message) {
    if (!notificationModal) return;

    notificationModal.classList.remove('hidden');

    if (type === 'success') {
        notificationIcon.innerHTML = '<i class="ph ph-check-circle text-green-500"></i>';
        notificationTitle.textContent = '¡Éxito!';
    } else {
        notificationIcon.innerHTML = '<i class="ph ph-warning-circle text-red-500"></i>';
        notificationTitle.textContent = 'Atención';
    }

    notificationMessage.textContent = decodeURIComponent(message).replace(/\+/g, ' ');
}

function closeNotificationModal() {
    if (notificationModal) {
        notificationModal.classList.add('hidden');
        // Limpiar URL para no mostrar notificación al recargar (opcional, pero buena UX)
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('message');
        url.searchParams.delete('tab');
        window.history.replaceState({}, '', url);
    }
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    const tab = urlParams.get('tab');

    // 1. Cambiar al tab correcto si se especifica
    if (tab) {
        switchTab(tab);
    } else {
        // Default tab
        switchTab('dashboard');
    }

    // 2. Mostrar notificación si hay status
    if (status && message) {
        showNotification(status, message);
    }
});
