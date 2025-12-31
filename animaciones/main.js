const LOGIN = document.getElementById('login');
const SEARCH_TRIGGER = document.getElementById('search-trigger');
const SEARCH_CONTAINER = document.getElementById('search-container');
const SEARCH_INPUT = document.getElementById('search-input');
const LOGIN_SIDEBAR = document.getElementById('login-sidebar');
const CLOSE_LOGIN = document.getElementById('close-login');

// Elementos del Carrito
const CART_ICON = document.getElementById('cart-icon');
const CART_SIDEBAR = document.getElementById('cart-sidebar');
const CLOSE_CART = document.getElementById('close-cart');
const CONT_SHOPPING = document.getElementById('continue-shopping');
const SIDE_OVERLAY = document.getElementById('side-overlay');
const CART_ITEMS_CONT = document.getElementById('cart-items-container');
const CART_SUBTOTAL = document.getElementById('cart-subtotal');
const CHECKOUT_BTN = document.getElementById('checkout-btn');

window.addEventListener('scroll', function () {
    const topBar = document.getElementById('top-bar');
    const mainHeader = document.getElementById('main-header');

    if (window.scrollY > 30) {
        // Ocultar barra superior
        topBar.style.height = '0';
        topBar.style.padding = '0';
        topBar.style.opacity = '0';
        topBar.style.overflow = 'hidden';
        mainHeader.style.backgroundColor = 'white';
    } else {
        // Mostrar barra superior
        topBar.style.height = '';
        topBar.style.padding = '';
        topBar.style.opacity = '1';
        topBar.style.overflow = '';
    }
});

// Función para cerrar todos los sidebars
function closeSidebars() {
    [LOGIN_SIDEBAR, CART_SIDEBAR].forEach(sidebar => {
        if (sidebar) {
            sidebar.classList.remove('login-sidebar-open');
            sidebar.classList.add('login-sidebar-close');
        }
    });
    if (SIDE_OVERLAY) SIDE_OVERLAY.classList.add('hidden');
    if (LOGIN) LOGIN.style.color = 'black';
}

// LOGIN EVENTS
if (LOGIN) {
    LOGIN.addEventListener('click', function () {
        if (LOGIN_SIDEBAR.classList.contains('login-sidebar-open')) {
            closeSidebars();
        } else {
            closeSidebars();
            LOGIN_SIDEBAR.classList.add('login-sidebar-open');
            LOGIN_SIDEBAR.classList.remove('login-sidebar-close');
            if (SIDE_OVERLAY) SIDE_OVERLAY.classList.remove('hidden');
            LOGIN.style.color = ' rgb(212 175 55 / var(--tw-text-opacity, 1))';
        }
    });
}

// SEARCH EVENTS
if (SEARCH_TRIGGER) {
    SEARCH_TRIGGER.addEventListener('click', function (e) {
        e.stopPropagation();
        if (SEARCH_CONTAINER.classList.contains('hidden')) {
            SEARCH_CONTAINER.classList.remove('hidden');
            setTimeout(() => {
                SEARCH_INPUT.focus();
            }, 100);
        } else {
            SEARCH_CONTAINER.classList.add('hidden');
        }
    });
}

// Cerrar buscador al pulsar fuera
document.addEventListener('click', function (e) {
    if (SEARCH_CONTAINER && !SEARCH_CONTAINER.contains(e.target) && e.target !== SEARCH_TRIGGER) {
        SEARCH_CONTAINER.classList.add('hidden');
    }
});

// Evitar que clicks dentro del buscador lo cierren
if (SEARCH_CONTAINER) {
    SEARCH_CONTAINER.addEventListener('click', (e) => e.stopPropagation());
}

if (SEARCH_INPUT) {
    SEARCH_INPUT.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const value = SEARCH_INPUT.value.trim();
            if (value) {
                window.location.href = `index.php?search=${encodeURIComponent(value)}`;
            } else {
                window.location.href = 'index.php';
            }
        }
    });
}

if (CLOSE_LOGIN) {
    CLOSE_LOGIN.addEventListener('click', closeSidebars);
}

// CART EVENTS
if (CART_ICON) {
    CART_ICON.addEventListener('click', function () {
        if (CART_SIDEBAR.classList.contains('login-sidebar-open')) {
            closeSidebars();
        } else {
            closeSidebars();
            CART_SIDEBAR.classList.add('login-sidebar-open');
            CART_SIDEBAR.classList.remove('login-sidebar-close');
            if (SIDE_OVERLAY) SIDE_OVERLAY.classList.remove('hidden');
            loadCart();
        }
    });
}

if (CLOSE_CART) CLOSE_CART.addEventListener('click', closeSidebars);
if (CONT_SHOPPING) CONT_SHOPPING.addEventListener('click', closeSidebars);
if (SIDE_OVERLAY) SIDE_OVERLAY.addEventListener('click', closeSidebars);

// Función para cargar el carrito dinámicamente
async function loadCart() {
    if (!CART_ITEMS_CONT) return;

    CART_ITEMS_CONT.innerHTML = '<p class="text-sm text-gray-500 text-center py-10">Actualizando cesta...</p>';

    try {
        const response = await fetch('../modelos/carrito/obtener-carrito.php');
        const data = await response.json();

        if (data.success && data.items.length > 0) {
            let html = '';
            data.items.forEach(item => {
                html += `
                    <div class="flex gap-4 group relative">
                        <div class="w-20 aspect-[3/4] bg-gray-50 overflow-hidden rounded-md">
                            <img src="../${item.imagen}" alt="${item.nombre}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 flex flex-col justify-between py-1">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h4 class="text-xs font-bold uppercase tracking-widest text-fashion-black pr-4">${item.nombre}</h4>
                                    <button onclick="removeFromCart('${item.key}')" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <i class="ph ph-trash text-sm"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase">Talla: ${item.talla} | Cant: ${item.cantidad}</p>
                            </div>
                            <p class="text-xs font-medium">${item.precio} €</p>
                        </div>
                    </div>
                `;
            });
            CART_ITEMS_CONT.innerHTML = html;
            if (CART_SUBTOTAL) CART_SUBTOTAL.textContent = data.subtotal_f;

            // Habilitar botón de pago
            if (CHECKOUT_BTN) {
                CHECKOUT_BTN.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                CHECKOUT_BTN.classList.add('bg-fashion-black', 'text-white', 'hover:bg-fashion-accent');
            }
        } else {
            CART_ITEMS_CONT.innerHTML = `
                <div class="text-center py-20 space-y-4">
                    <i class="ph ph-handbag text-5xl text-gray-200"></i>
                    <p class="text-sm text-gray-500 uppercase tracking-widest leading-loose">Tu cesta está vacía</p>
                </div>
            `;
            if (CART_SUBTOTAL) CART_SUBTOTAL.textContent = '0,00 €';

            // Deshabilitar botón de pago
            if (CHECKOUT_BTN) {
                CHECKOUT_BTN.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                CHECKOUT_BTN.classList.remove('bg-fashion-black', 'text-white', 'hover:bg-fashion-accent');
            }
        }
    } catch (error) {
        console.error('Error al cargar el carrito:', error);
        CART_ITEMS_CONT.innerHTML = '<p class="text-xs text-red-500 text-center py-10 uppercase tracking-widest">Error al conectar con la cesta</p>';
    }
}

// Función para eliminar un item del carrito
async function removeFromCart(key) {
    try {
        const response = await fetch('../modelos/carrito/eliminar-item-carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ key: key })
        });

        const result = await response.json();

        if (result.success) {
            // Recargar el carrito para mostrar cambios
            loadCart();

            // Actualizar contador en la cabecera
            const badge = document.getElementById('cart-count-badge');
            if (badge) {
                badge.textContent = result.total_items;
                if (result.total_items === 0) {
                    badge.classList.add('hidden');
                }
            }
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error al eliminar del carrito:', error);
    }
}
