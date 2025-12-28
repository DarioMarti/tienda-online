
const editBtn = document.getElementById('edit-profile-btn');
const modal = document.getElementById('edit-modal');
const closeModalBtn = document.getElementById('close-modal');
const cancelBtn = document.getElementById('cancel-btn');
const editForm = document.getElementById('edit-profile-form');
const btnCambiarContrasena = document.getElementById('btnCambiarContrasena');

if (editBtn) {
    // Abrir modal
    editBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    // Cerrar modal
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Cerrar al hacer clic fuera del modal
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Cerrar con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Manejar envío del formulario con AJAX
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);

            // Log de datos que se envían
            console.log('=== ENVIANDO DATOS ===');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            try {
                console.log('Enviando petición...');

                const response = await fetch('../modelos/usuarios/modificar-usuario.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Respuesta recibida, status:', response.status);

                const responseText = await response.text();
                console.log('Respuesta raw:', responseText);

                const result = JSON.parse(responseText);
                console.log('Respuesta parseada:', result);

                closeModal();

                if (result.success) {
                    console.log('✅ Actualización exitosa');
                    showResultModal('success', result.message);
                    updateProfileData(result.data);
                } else {
                    console.log('❌ Error:', result.message);
                    showResultModal('error', result.message);
                }

            } catch (error) {
                console.error('❌ EXCEPCIÓN:', error);
                closeModal();
                showResultModal('error', 'Error de conexión. Por favor, intenta de nuevo.');
            }
        });
    }

}

// === LÓGICA PARA CAMBIAR CONTRASEÑA ===
const passwordModal = document.getElementById('change-password-modal');
const closePasswordBtn = document.getElementById('close-password-modal');
const cancelPasswordBtn = document.getElementById('cancel-password-btn');
const passwordForm = document.getElementById('change-password-form');

if (btnCambiarContrasena) {
    btnCambiarContrasena.addEventListener('click', () => {
        if (passwordModal) {
            passwordModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (passwordForm) passwordForm.reset();
        }
    });
}

function closePasswordModal() {
    if (passwordModal) {
        passwordModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

if (closePasswordBtn) closePasswordBtn.addEventListener('click', closePasswordModal);
if (cancelPasswordBtn) cancelPasswordBtn.addEventListener('click', closePasswordModal);

// Cerrar al hacer clic fuera
if (passwordModal) {
    passwordModal.addEventListener('click', (e) => {
        if (e.target === passwordModal) closePasswordModal();
    });
}

// Manejar envío del formulario de contraseña
if (passwordForm) {
    passwordForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(passwordForm);
        const newPass = formData.get('new_password');
        const confirmPass = formData.get('confirm_password');

        if (newPass !== confirmPass) {
            showResultModal('error', 'Las contraseñas nuevas no coinciden');
            return;
        }

        try {
            const response = await fetch('../modelos/usuarios/cambiar-contrasena.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            closePasswordModal();

            if (result.success) {
                showResultModal('success', result.message);
            } else {
                showResultModal('error', result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            closePasswordModal();
            showResultModal('error', 'Error de conexión');
        }
    });
}

// Función para mostrar modal de resultado
function showResultModal(type, message) {
    const resultModal = document.getElementById('result-modal');
    const resultIcon = document.getElementById('result-icon');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');

    if (!resultModal) return;

    if (type === 'success') {
        resultIcon.innerHTML = '<i class="ph ph-check-circle text-6xl text-green-500"></i>';
        resultTitle.textContent = '¡Éxito!';
        resultMessage.textContent = message;
    } else {
        resultIcon.innerHTML = '<i class="ph ph-x-circle text-6xl text-red-500"></i>';
        resultTitle.textContent = 'Error';
        resultMessage.textContent = message;
    }

    resultModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar modal de resultado
function closeResultModal() {
    const resultModal = document.getElementById('result-modal');
    if (resultModal) {
        resultModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Cerrar modal de resultado
const closeResultBtn = document.getElementById('close-result-modal');
if (closeResultBtn) {
    closeResultBtn.addEventListener('click', closeResultModal);
}

const resultModal = document.getElementById('result-modal');
if (resultModal) {
    resultModal.addEventListener('click', (e) => {
        if (e.target.id === 'result-modal') {
            closeResultModal();
        }
    });
}

// Función para actualizar los datos en la página
function updateProfileData(data) {
    console.log('Actualizando datos en la página:', data);

    // Actualizar encabezado
    const headerName = document.querySelector('h1.font-editorial');
    if (headerName) {
        headerName.textContent = `${data.nombre} ${data.apellidos}`;
        console.log('✅ Encabezado actualizado');
    }

    // Actualizar nombre en el header
    const menuUserName = document.querySelector('.ph-user-circle + span');
    if (menuUserName) {
        menuUserName.textContent = data.nombre;
        console.log('✅ Nombre en menú actualizado');
    }

    // Actualizar campos de datos personales
    const datosPersonales = document.querySelectorAll('.bg-fashion-gray.border.border-gray-200');
    console.log(`Encontrados ${datosPersonales.length} campos para actualizar`);

    datosPersonales.forEach((campo, index) => {
        const label = campo.previousElementSibling;
        if (!label) return;

        const labelText = label.textContent.trim().toLowerCase();
        console.log(`Campo ${index}: ${labelText}`);

        if (labelText.includes('nombre') && !labelText.includes('apellido')) {
            campo.textContent = data.nombre;
            console.log(`✅ Actualizado nombre: ${data.nombre}`);
        } else if (labelText.includes('apellido')) {
            campo.textContent = data.apellidos;
            console.log(`✅ Actualizado apellidos: ${data.apellidos}`);
        } else if (labelText.includes('teléfono') || labelText.includes('telefono')) {
            campo.textContent = data.telefono || 'No especificado';
            console.log(`✅ Actualizado teléfono: ${data.telefono}`);
        } else if (labelText.includes('dirección') || labelText.includes('direccion')) {
            campo.textContent = data.direccion || 'No especificada';
            console.log(`✅ Actualizado dirección: ${data.direccion}`);
        }
    });
}

// === LÓGICA PARA ELIMINAR CUENTA ===
{
    const deleteBtn = document.getElementById('eliminar-cuenta');
    const profileDeleteModal = document.getElementById('delete-account-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

    if (deleteBtn) {
        deleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (profileDeleteModal) {
                profileDeleteModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        });
    }

    function closeProfileDeleteModal() {
        if (profileDeleteModal) {
            profileDeleteModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeProfileDeleteModal);
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            // Redirigir al script de eliminación
            window.location.href = '../modelos/usuarios/eliminar-usuario.php';
        });
    }

    // Cerrar al hacer clic fuera del modal
    if (profileDeleteModal) {
        profileDeleteModal.addEventListener('click', (e) => {
            if (e.target === profileDeleteModal) {
                closeProfileDeleteModal();
            }
        });
    }
}



// CONTAR LOS PEDIDOS DEL USUARIO (Datos pasados desde PHP)
if (typeof userOrders !== 'undefined') {
    userOrders.forEach(pedido => {
        console.log('Pedido:', pedido);
    });
    console.log('Total pedidos:', userOrders.length);
}

