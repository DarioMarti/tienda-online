// Gestión de Tabs
function switchTab(tabId, updateUrl = true) {
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

        // Persistencia
        localStorage.setItem('activeAdminTab', tabId);

        if (updateUrl) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        }
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

// Manejar envíos de formularios con AJAX (Delegación de eventos para mayor robustez)
document.addEventListener('submit', async (e) => {
    const form = e.target;

    // Lista de formularios que manejamos por AJAX
    const ajaxForms = ['user-form', 'product-form', 'category-form', 'order-form'];


    if (ajaxForms.includes(form.id)) {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.textContent = 'Guardando...';
        }

        console.log(`[AJAX] Enviando ${form.id}...`);
        const formData = new FormData(form);

        // Debug: Log de campos enviados
        for (let [key, value] of formData.entries()) {
            console.log(`[AJAX Payload] ${key}:`, value instanceof File ? `Archivo: ${value.name}` : value);
        }

        // Lógica específica para productos: Recoger tallas
        if (form.id === 'product-form') {
            const sizes = [];
            const rows = form.querySelectorAll('.size-row');
            const uniqueSizes = new Set();
            let hasDuplicates = false;

            rows.forEach(row => {
                const tallaInput = row.querySelector('input[name="talla_input[]"]');
                const tallaRaw = tallaInput ? tallaInput.value.trim() : '';
                const talla = tallaRaw.toUpperCase();
                const stock = 0;

                if (tallaRaw) {
                    if (uniqueSizes.has(talla)) {
                        hasDuplicates = true;
                    }
                    uniqueSizes.add(talla);
                    sizes.push({ talla: tallaRaw, stock });
                }
            });

            if (hasDuplicates) {
                showNotification('error', 'No puedes repetir la misma talla dos veces.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.dataset.originalText;
                }
                return;
            }

            formData.append('tallas_stock', JSON.stringify(sizes));
            console.log("[AJAX Payload] tallas_stock (JSON):", JSON.stringify(sizes));
        }

        try {
            const url = form.getAttribute('action') || form.action;
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const clonedResponse = response.clone();
            const responseText = await clonedResponse.text();
            console.log(`[AJAX] Respuesta bruta:`, responseText);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error("[AJAX] Error al parsear JSON:", e);
                throw new Error("La respuesta del servidor no es un JSON válido. Revisa la consola.");
            }

            console.log('Respuesta JSON parseada:', result);

            if (result.success) {
                if (form.id === 'user-form') closeUserModal();
                if (form.id === 'product-form') closeProductModal();
                if (form.id === 'category-form') closeCategoryModal();
                if (form.id === 'order-form') closeOrderModal();

                showNotification('success', result.message);
                window.pendingReload = true;
            } else {
                showNotification('error', result.message);
            }
        } catch (error) {
            console.error('Error en proceso AJAX:', error);
            showNotification('error', 'Error: ' + error.message);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText;
            }
        }
    }
});

// Gestión del Modal de Eliminación
const adminDeleteModal = document.getElementById('delete-modal');
const deleteMessage = document.getElementById('delete-message');
let deleteTargetUrl = '';

function openDeleteModal(url, message) {
    if (!adminDeleteModal) return;
    deleteTargetUrl = url;
    const msgEl = document.getElementById('delete-message');
    if (msgEl) msgEl.textContent = message;
    adminDeleteModal.classList.remove('hidden');
}

function closeDeleteModal() {
    if (adminDeleteModal) {
        adminDeleteModal.classList.add('hidden');
        deleteTargetUrl = '';
    }
}

function confirmDelete() {
    if (deleteTargetUrl) {
        window.location.href = deleteTargetUrl;
    }
}

function deleteUser(id, nombre) {
    const url = `../modelos/usuarios/admin-eliminar-usuario.php?id=${id}`;
    const msg = `¿Deseas eliminar al usuario "${nombre}"? Esta acción no se puede deshacer.`;
    openDeleteModal(url, msg);
}

// Placeholder functions for new buttons
// Gestión del Modal de Pedidos
const orderModal = document.getElementById('order-modal');
const orderForm = document.getElementById('order-form');
const orderModalTitle = document.getElementById('order-modal-title');
const orderIdInput = document.getElementById('order_id');

function openOrderModal() {
    orderModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Resetear formulario
    orderForm.reset();
    orderModalTitle.textContent = 'Nuevo Pedido';
    orderForm.action = '../modelos/pedidos/crear-pedido.php';
    orderIdInput.value = '';

    // Limpiar productos y añadir una fila vacía
    document.getElementById('order-items-builder').innerHTML = '';
    addProductRow();
    document.getElementById('order_coste_total').value = '0.00';
    window.editingOrderOriginalItems = null; // Limpiar para nuevo pedido
    validateStock();
}



function closeOrderModal() {
    orderModal.classList.add('hidden');
    document.getElementById('order-stock-warning').classList.add('hidden');
    document.getElementById('stock-warning-list').innerHTML = '';
    document.body.style.overflow = 'auto';
}

function editOrder(order) {
    openOrderModal();
    orderModalTitle.textContent = 'Editar Pedido';
    orderForm.action = '../modelos/pedidos/modificar-pedido.php';
    orderIdInput.value = order.id;

    document.getElementById('order_usuario_email').value = order.usuario_email || '';

    document.getElementById('order_coste_total').value = order.coste_total;
    document.getElementById('order_nombre_destinatario').value = order.nombre_destinatario;
    document.getElementById('order_direccion_envio').value = order.direccion_envio;
    document.getElementById('order_ciudad').value = order.ciudad;
    document.getElementById('order_provincia').value = order.provincia;
    document.getElementById('order_estado').value = order.estado;

    // Cargar productos del pedido
    const itemsContainer = document.getElementById('order-items-builder');
    itemsContainer.innerHTML = ''; // Limpiar

    fetch(`../modelos/pedidos/obtener-detalle-pedido.php?id=${order.id}`)
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Para validación en edición: Guardar stock original localmente para este modal
                window.editingOrderOriginalItems = result.items;

                result.items.forEach(item => {
                    addProductRow(item);
                });
                calculateOrderTotal();
                validateStock();
            }
        });
}


function deleteOrder(id) {
    const url = `../modelos/pedidos/eliminar-pedido.php?id=${id}&redirect=true`;
    const msg = `¿Deseas eliminar el pedido #${id}? Esta acción no se puede deshacer.`;
    openDeleteModal(url, msg);
}



// Gestión del Modal de Detalles de Pedido
const orderDetailsModal = document.getElementById('order-details-modal');

async function viewOrderDetails(id) {
    if (!orderDetailsModal) return;

    try {
        const response = await fetch(`../modelos/pedidos/obtener-detalle-pedido.php?id=${id}`);
        const result = await response.json();

        if (result.success) {
            const p = result.pedido;
            const items = result.items;

            // Rellenar cabecera
            document.getElementById('view_order_id').textContent = `#${p.id}`;
            document.getElementById('view_order_date').textContent = new Date(p.fecha).toLocaleString();

            // Rellenar cliente
            document.getElementById('view_customer_name').textContent = p.nombre_destinatario;
            document.getElementById('view_customer_email').textContent = p.usuario_email || 'Venta Anónima';

            // Badge de estado
            const badge = document.getElementById('view_order_status_badge');
            badge.textContent = p.estado.toUpperCase();
            badge.className = 'px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase ';
            const classes = {
                'pendiente': 'bg-yellow-100 text-yellow-700',
                'pagado': 'bg-green-100 text-green-700',
                'enviado': 'bg-blue-100 text-blue-700',
                'entregado': 'bg-purple-100 text-purple-700',
                'cancelado': 'bg-red-100 text-red-700'
            };
            badge.className += (classes[p.estado] || 'bg-gray-100 text-gray-700');

            // Dirección
            document.getElementById('view_shipping_address').textContent = p.direccion_envio;
            document.getElementById('view_shipping_location').textContent = `${p.ciudad}, ${p.provincia}`;

            // Items
            const itemsList = document.getElementById('order_items_list');
            itemsList.innerHTML = '';
            items.forEach(item => {
                const total = (item.cantidad * item.precio_unitario).toFixed(2);
                itemsList.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="../${item.producto_imagen}" class="w-8 h-8 object-cover rounded shadow-sm">
                                <span class="font-medium text-fashion-black">${item.producto_nombre}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold">${item.cantidad}</td>
                        <td class="px-6 py-4 text-right">${item.precio_unitario} €</td>
                        <td class="px-6 py-4 text-right font-bold text-fashion-black">${total} €</td>
                    </tr>
                `;
            });

            // Total
            document.getElementById('view_order_total').textContent = `${parseFloat(p.coste_total).toFixed(2)} €`;

            // Mostrar modal
            orderDetailsModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Error al cargar detalles del pedido');
    }
}

function closeOrderDetailsModal() {
    if (orderDetailsModal) {
        orderDetailsModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}


// Cerrar modal al hacer clic fuera
if (orderModal) {
    orderModal.addEventListener('click', (e) => {
        if (e.target === orderModal) closeOrderModal();
    });
}

if (orderDetailsModal) {
    orderDetailsModal.addEventListener('click', (e) => {
        if (e.target === orderDetailsModal) closeOrderDetailsModal();
    });
}



// Lógica de Selección de Productos en Pedidos
function addProductRow(itemData = null) {
    const container = document.getElementById('order-items-builder');
    if (!container) return;

    const rowId = Date.now() + Math.random();
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50 transition-colors order-item-row';
    tr.id = `row-${rowId}`;

    let options = '<option value="">Seleccionar Producto...</option>';
    allProducts.forEach(p => {
        const selected = (itemData && itemData.producto_id == p.id) ? 'selected' : '';
        options += `<option value="${p.id}" data-precio="${p.precio}" ${selected}>${p.nombre}</option>`;
    });

    tr.innerHTML = `
        <td class="px-4 py-3">
            <select name="producto_id[]" required onchange="updateRowSubtotal(this)"
                class="w-full text-xs border border-gray-200 rounded p-2 focus:outline-none focus:border-fashion-black">
                ${options}
            </select>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center border border-gray-300 rounded overflow-hidden w-32 bg-white shadow-sm quantity-container">
                <button type="button" onclick="adjustQuantity(this, -1)" 
                    class="w-10 h-9 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-800 transition-colors border-r border-gray-200 text-base font-bold shrink-0">-</button>
                <input type="number" name="cantidad[]" value="${itemData ? itemData.cantidad : 1}" min="1" step="1" required
                    oninput="updateRowSubtotal(this)"
                    class="w-full text-sm py-1 focus:outline-none text-center quantity-input bg-white text-gray-900 font-bold h-9 px-1">
                <button type="button" onclick="adjustQuantity(this, 1)" 
                    class="w-10 h-9 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-800 transition-colors border-l border-gray-200 text-base font-bold shrink-0">+</button>
            </div>
        </td>
        <td class="px-4 py-3 text-right text-xs text-gray-400">
            <span class="unit-price">0.00</span> €
        </td>
        <td class="px-4 py-3 text-right text-xs font-bold text-fashion-black">
            <span class="subtotal">0.00</span> €
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeProductRow(this)" class="text-gray-300 hover:text-red-500 transition-colors">
                <i class="ph ph-trash"></i>
            </button>
        </td>
    `;

    container.appendChild(tr);
    // Inicializar subtotal para esta fila
    const select = tr.querySelector('select');
    updateRowSubtotal(select);
}

function removeProductRow(btn) {
    const row = btn.closest('tr');
    if (row) {
        row.remove();
        calculateOrderTotal();
        validateStock();
    }
}

function adjustQuantity(btn, delta) {
    const container = btn.closest('.quantity-container');
    const input = container.querySelector('.quantity-input');
    let val = parseInt(input.value) || 0;
    val += delta;
    if (val < 1) val = 1;
    input.value = val;
    updateRowSubtotal(input);
}


function updateRowSubtotal(element) {
    const row = element.closest('tr');
    if (!row) return;

    const select = row.querySelector('select');
    const quantityInput = row.querySelector('.quantity-input');
    const unitPriceSpan = row.querySelector('.unit-price');
    const subtotalSpan = row.querySelector('.subtotal');

    if (!select || !quantityInput || !unitPriceSpan || !subtotalSpan) return;

    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.dataset.precio) || 0;
    const quantity = parseInt(quantityInput.value) || 0;

    const subtotal = price * quantity;

    unitPriceSpan.textContent = price.toFixed(2);
    subtotalSpan.textContent = subtotal.toFixed(2);

    calculateOrderTotal();
    validateStock();
}


function validateStock() {
    let allValid = true;
    const rows = document.querySelectorAll('.order-item-row');
    const submitBtn = document.querySelector('#order-form button[type="submit"]');
    const globalWarning = document.getElementById('order-stock-warning');
    const warningList = document.getElementById('stock-warning-list');

    if (!globalWarning || !warningList) return;

    warningList.innerHTML = '';
    let errors = [];

    rows.forEach(row => {
        const select = row.querySelector('select');
        const quantityInput = row.querySelector('.quantity-input');
        const quantityContainer = row.querySelector('.quantity-container');

        if (!select || !quantityInput) return;

        const pId = select.value;
        const qty = parseInt(quantityInput.value) || 0;

        if (pId) {
            const product = typeof allProducts !== 'undefined' ? allProducts.find(p => p.id == pId) : null;
            if (product) {
                let availableStock = parseInt(product.stock) || 0;

                if (window.editingOrderOriginalItems) {
                    const originalItem = window.editingOrderOriginalItems.find(it => it.producto_id == pId);
                    if (originalItem) {
                        availableStock += parseInt(originalItem.cantidad) || 0;
                    }
                }

                if (qty > availableStock) {
                    allValid = false;
                    if (quantityContainer) quantityContainer.classList.add('border-red-500', 'bg-red-50');
                    errors.push(`<strong>${product.nombre}</strong>: Has solicitado ${qty} pero solo hay ${availableStock} disponibles.`);
                } else {
                    if (quantityContainer) quantityContainer.classList.remove('border-red-500', 'bg-red-50');
                }
            }
        }
    });

    if (errors.length > 0) {
        globalWarning.classList.remove('hidden');
        errors.forEach(err => {
            const li = document.createElement('li');
            li.innerHTML = err;
            warningList.appendChild(li);
        });
    } else {
        globalWarning.classList.add('hidden');
    }

    if (submitBtn) {
        if (!allValid) {
            submitBtn.disabled = true;
            submitBtn.style.setProperty('background-color', '#9ca3af', 'important');
            submitBtn.style.setProperty('opacity', '0.6', 'important');
            submitBtn.style.setProperty('cursor', 'not-allowed', 'important');
            submitBtn.classList.remove('hover:bg-fashion-accent');
        } else {
            submitBtn.disabled = false;
            submitBtn.style.backgroundColor = '';
            submitBtn.style.opacity = '';
            submitBtn.style.cursor = '';
            submitBtn.classList.add('hover:bg-fashion-accent');
        }
    }
}







function calculateOrderTotal() {
    let total = 0;
    document.querySelectorAll('#order-items-builder .subtotal').forEach(span => {
        total += parseFloat(span.textContent) || 0;
    });
    const totalInput = document.getElementById('order_coste_total');
    if (totalInput) totalInput.value = total.toFixed(2);
}


// Gestión del Modal de Productos
const productModal = document.getElementById('product-modal');
const productForm = document.getElementById('product-form');
const productModalTitle = document.getElementById('product-modal-title');
const productFormAction = document.getElementById('product_form_action');
const productIdInput = document.getElementById('product_id');
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('prod_imagen');
const imagePreview = document.getElementById('image-preview');
const uploadPlaceholder = document.getElementById('upload-placeholder');
const changeImageOverlay = document.getElementById('change-image-overlay');

function openProductModal() {
    productModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Resetear formulario
    productForm.reset();
    productModalTitle.textContent = 'Nuevo Producto';
    productForm.setAttribute('action', '../modelos/productos/agregar-producto.php');
    productFormAction.value = 'create';
    productIdInput.value = '';

    // Resetear preview
    imagePreview.src = '#';
    imagePreview.classList.add('hidden');
    uploadPlaceholder.classList.remove('hidden');
    changeImageOverlay.classList.add('hidden');

    // Inicializar tallas
    // Inicializar tallas
    const sizeContainer = document.getElementById('sizes-container');
    if (sizeContainer) {
        sizeContainer.innerHTML = ''; // Limpiar contenedor
        // Pequeño timeout para asegurar que el DOM está listo si hay lag
        setTimeout(() => {
            if (typeof addSizeRow === 'function') {
                addSizeRow();
            } else {
                console.error("addSizeRow no está definida");
            }
        }, 10);
    }
}


// ==========================================
// Gestión de Tallas (Robustez Total)
// ==========================================

// Inicialización única del datalist
document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('size-suggestions')) {
        const datalist = document.createElement('datalist');
        datalist.id = 'size-suggestions';
        const suggestions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', 'Talla Única'];
        suggestions.forEach(s => {
            const option = document.createElement('option');
            option.value = s;
            datalist.appendChild(option);
        });
        document.body.appendChild(datalist);
    }
});

// Delegación de Eventos Global a nivel de Documento
// Esto asegura que funcione sin importar cuándo o cómo se carguen los elementos
document.addEventListener('click', (e) => {
    // Debug info
    console.log('Click detected on:', e.target);

    // 1. Botón Añadir Talla
    const addBtn = e.target.closest('#add-size-btn');
    if (addBtn) {
        // alert("DEBUG: Click detectado en botón!");
        console.log('Botón Añadir Talla clickeado');
        e.preventDefault();
        e.stopPropagation(); // Evitar propagación
        addSizeRow();
        return;
    }

    // 2. Botón Eliminar Talla
    const deleteBtn = e.target.closest('.delete-size-btn');
    if (deleteBtn) {
        e.preventDefault();
        e.stopPropagation();
        deleteBtn.closest('.size-row').remove();
        return;
    }
});

// Función para añadir fila
// Función para añadir fila
function addSizeRow(talla = '') {
    // ALERT DEBUG 1: Función llamada
    console.log("addSizeRow called");

    const container = document.getElementById('sizes-container');
    if (!container) {
        console.error("ERROR CRÍTICO: No se encuentra el contenedor #sizes-container");
        return;
    }

    const div = document.createElement('div');
    // Forzamos visibilidad con fondo rojo temporal para debug -> Volvemos a limpio
    div.className = 'flex gap-4 items-center size-row mb-2 relative';
    /*
    div.style.backgroundColor = '#ffecec';
    div.style.border = '1px solid red';
    div.style.padding = '10px';
    */

    div.innerHTML = `
        <div class="relative flex-1">
            <input type="text" name="talla_input[]" value="${talla}" list="size-suggestions" placeholder="Talla (Ej: S, 38)" 
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-fashion-black text-sm bg-white font-bold text-fashion-black" autocomplete="off">
        </div>
        
        <button type="button" class="delete-size-btn text-red-500 hover:text-red-700 p-2 transform hover:scale-110 transition-transform bg-white rounded border border-red-200">
            <i class="ph ph-trash text-lg"></i>
        </button>
    `;

    try {
        container.appendChild(div);
    } catch (e) {
        console.error("Error al hacer appendChild: " + e.message);
    }

    // Auto-focus
    if (talla === '') {
        try {
            const input = div.querySelector('input[type="text"]');
            if (input) input.focus();
        } catch (e) { console.error(e); }
    }
}



// Exponer explícitamente a window para debugging y acceso global garantizado
window.addSizeRow = addSizeRow;


function closeProductModal() {
    productModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Drag & Drop Logic
if (dropZone) {
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.querySelector('#image-preview-container').classList.add('border-fashion-black', 'bg-gray-100');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.querySelector('#image-preview-container').classList.remove('border-fashion-black', 'bg-gray-100');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.querySelector('#image-preview-container').classList.remove('border-fashion-black', 'bg-gray-100');

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            previewImage(fileInput);
        }
    });
}

function previewImage(input) {
    console.log('previewImage called', input);
    const imagePreview = document.getElementById('image-preview');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const changeImageOverlay = document.getElementById('change-image-overlay');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log('Image loaded', e.target.result);
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('hidden');
            uploadPlaceholder.classList.add('hidden');
            changeImageOverlay.classList.remove('hidden');
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Cerrar modal de producto al hacer clic fuera
if (productModal) {
    productModal.addEventListener('click', (e) => {
        if (e.target === productModal) {
            closeProductModal();
        }
    });
}

// (El manejo de envío AJAX ahora está centralizado arriba con delegación de eventos)

// Función para ajustar valores numéricos (Precio/Stock)
function adjustValue(inputId, step) {
    const input = document.getElementById(inputId);
    let val = parseFloat(input.value) || 0;
    val += step;

    // Validar mínimos
    if (val < 0) val = 0;

    // Formato para precio (2 decimales) vs stock (entero)
    if (step % 1 !== 0) {
        input.value = val.toFixed(2);
    } else {
        input.value = Math.round(val);
    }
}

function editProduct(producto) {
    productModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Rellenar formulario
    productModalTitle.textContent = 'Modificar Producto';
    productForm.setAttribute('action', '../modelos/productos/modificar-producto.php');
    productFormAction.value = 'update';
    productIdInput.value = producto.id;

    document.getElementById('prod_nombre').value = producto.nombre;
    document.getElementById('prod_precio').value = producto.precio;
    document.getElementById('prod_stock').value = producto.stock;
    document.getElementById('prod_categoria_id').value = producto.categoria_id;
    document.getElementById('prod_descripcion').value = producto.descripcion;

    // Cargar imagen en preview
    if (producto.imagen) {
        imagePreview.src = '../' + producto.imagen;
        imagePreview.classList.remove('hidden');
        uploadPlaceholder.classList.add('hidden');
        changeImageOverlay.classList.remove('hidden');
    }

    // Gestionar tallas
    const sizeContainer = document.getElementById('sizes-container');
    if (sizeContainer) {
        sizeContainer.innerHTML = '';

        // Fetch para obtener tallas del producto
        fetch(`../modelos/productos/obtener-tallas-producto.php?id=${producto.id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.tallas && data.tallas.length > 0) {
                    data.tallas.forEach(t => {
                        addSizeRow(t.talla);
                    });
                } else {
                    // Si no tiene tallas, añadir una fila vacía
                    addSizeRow();
                }
            })
            .catch(err => {
                console.error('Error al cargar tallas:', err);
                addSizeRow();
            });
    }
}

function deleteProduct(id, nombre) {
    const url = `../modelos/productos/eliminar-producto.php?id=${id}`;
    const msg = `¿Deseas eliminar el producto "${nombre}"? Esta acción no se puede deshacer.`;
    openDeleteModal(url, msg);
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
    // Set Action to Create
    categoryForm.action = '../modelos/categorias/crear-categoria.php';

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
    // Set Action to Update
    categoryForm.action = '../modelos/categorias/modificar-categoria.php';

    categoryIdInput.value = category.id;

    // Rellenar datos
    document.getElementById('cat_nombre').value = category.nombre;
    document.getElementById('cat_descripcion').value = category.descripcion;
    document.getElementById('cat_parent_id').value = category.categoria_padre_id || '';
}

function deleteCategory(id, nombre) {
    const url = `../modelos/categorias/eliminar-categoria.php?id=${id}`;
    const msg = `¿Deseas eliminar la categoría "${nombre}"? Esta acción no se puede deshacer.`;
    openDeleteModal(url, msg);
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

    // Ya no usamos decodeURIComponent porque los mensajes de AJAX vienen como texto plano
    // y esto provocaba fallos si el mensaje contenía caracteres especiales.
    notificationMessage.textContent = message;
}


function closeNotificationModal() {
    notificationModal.classList.add('hidden');

    if (window.pendingReload) {
        const activeTab = localStorage.getItem('activeAdminTab') || 'dashboard';
        const url = new URL(window.location.origin + window.location.pathname);
        url.searchParams.set('tab', activeTab);
        window.location.href = url.href;
    } else {
        // Limpiar URL para no mostrar notificación al recargar (opcional, pero buena UX)
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('message');
        // NO borramos 'tab' para que persista en la URL
        window.history.replaceState({}, '', url);
    }
}



// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    const urlTab = urlParams.get('tab');
    const storedTab = localStorage.getItem('activeAdminTab');

    // 1. Determinar tab a mostrar: URL > LocalStorage > Default
    const tabToShow = urlTab || storedTab || 'dashboard';
    switchTab(tabToShow, false); // No actualizar URL al inicio para no ensuciar el history si ya está bien

    // 2. Mostrar notificación si hay status
    if (status && message) {
        showNotification(status, message);
    }
});

