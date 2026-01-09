
// ELEMENTOS GLOBALES
const DISPARADOR_BUSQUEDA = document.getElementById('disparador-busqueda');
const CONTENEDOR_BUSQUEDA = document.getElementById('contenedor-busqueda');
const INPUT_BUSQUEDA = document.getElementById('input-busqueda');

const BOTON_LOGIN = document.getElementById('btn-login');
const SIDEBAR_LOGIN = document.getElementById('sidebar-login');
const CERRAR_LOGIN = document.getElementById('cerrar-login');

const ICONO_CARRITO = document.getElementById('icono-carrito');
const SIDEBAR_CARRITO = document.getElementById('sidebar-carrito');
const CERRAR_CARRITO = document.getElementById('cerrar-carrito');
const CONTINUAR_COMPRANDO = document.getElementById('continuar-comprando');
const CAPA_SUPERPUESTA = document.getElementById('capa-superpuesta');
const CONTENEDOR_ITEMS_CARRITO = document.getElementById('contenedor-items-carrito');
const SUBTOTAL_CARRITO = document.getElementById('subtotal-carrito');
const BOTON_FINALIZAR_COMPRA = document.getElementById('btn-finalizar-compra');

// MENÚ MÓVIL
const DISPARADOR_MENU_MOVIL = document.getElementById('disparador-menu-movil');
const SIDEBAR_MENU_MOVIL = document.getElementById('sidebar-menu-movil');
const CERRAR_MENU_MOVIL = document.getElementById('cerrar-menu-movil');
const BOTON_LOGIN_MOVIL = document.getElementById('btn-login-movil');

// CONTROL DEL SCROLL EN CABECERA
window.addEventListener('scroll', () => {
    const barraSuperior = document.getElementById('barra-superior');
    const cabeceraPrincipal = document.getElementById('cabecera-principal');

    if (!barraSuperior || !cabeceraPrincipal) return;

    if (window.scrollY > 30) {
        barraSuperior.style.height = '0';
        barraSuperior.style.padding = '0';
        barraSuperior.style.opacity = '0';
        barraSuperior.style.overflow = 'hidden';
        cabeceraPrincipal.style.backgroundColor = 'white';
    } else {
        barraSuperior.style.height = '';
        barraSuperior.style.padding = '';
        barraSuperior.style.opacity = '1';
        barraSuperior.style.overflow = '';
    }
});

// GESTIÓN DE BARRAS LATERALES (LOGIN / CARRITO / MENÚ)
function cerrarSidebars() {
    [SIDEBAR_LOGIN, SIDEBAR_CARRITO, SIDEBAR_MENU_MOVIL].forEach(sidebar => {
        if (sidebar) {
            sidebar.classList.remove('sidebar-abierto');
            sidebar.classList.add('sidebar-cerrado');
        }
    });
    if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.add('hidden');
    if (BOTON_LOGIN) BOTON_LOGIN.style.color = '';
}

// TOGGLE LOGIN SIDEBAR
if (BOTON_LOGIN) {
    BOTON_LOGIN.addEventListener('click', () => {
        if (SIDEBAR_LOGIN.classList.contains('sidebar-abierto')) {
            cerrarSidebars();
        } else {
            cerrarSidebars();
            SIDEBAR_LOGIN.classList.add('sidebar-abierto');
            SIDEBAR_LOGIN.classList.remove('sidebar-cerrado');
            if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.remove('hidden');
            BOTON_LOGIN.style.color = '#D4AF37'; // Dorado
        }
    });
}

// TOGGLE CARRITO SIDEBAR
if (ICONO_CARRITO) {
    ICONO_CARRITO.addEventListener('click', () => {
        if (SIDEBAR_CARRITO.classList.contains('sidebar-abierto')) {
            cerrarSidebars();
        } else {
            cerrarSidebars();
            SIDEBAR_CARRITO.classList.add('sidebar-abierto');
            SIDEBAR_CARRITO.classList.remove('sidebar-cerrado');
            if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.remove('hidden');
            cargarCarrito();
        }
    });
}

// LISTENERS UNIFICADOS PARA CERRAR
[CERRAR_LOGIN, CERRAR_CARRITO, CERRAR_MENU_MOVIL, CONTINUAR_COMPRANDO, CAPA_SUPERPUESTA].forEach(el => {
    if (el) el.addEventListener('click', cerrarSidebars);
});

// TOGGLE MENÚ MÓVIL
if (DISPARADOR_MENU_MOVIL) {
    DISPARADOR_MENU_MOVIL.addEventListener('click', (e) => {
        e.stopPropagation();
        console.log("Menú móvil clickeado");
        if (SIDEBAR_MENU_MOVIL.classList.contains('sidebar-abierto')) {
            cerrarSidebars();
        } else {
            cerrarSidebars();
            SIDEBAR_MENU_MOVIL.classList.add('sidebar-abierto');
            SIDEBAR_MENU_MOVIL.classList.remove('sidebar-cerrado');
            if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.remove('hidden');
            console.log("Sidebar menú móvil abierto");
        }
    });
}

// LOGIN DESDE MENÚ MÓVIL
if (BOTON_LOGIN_MOVIL) {
    BOTON_LOGIN_MOVIL.addEventListener('click', () => {
        cerrarSidebars();
        setTimeout(() => {
            if (SIDEBAR_LOGIN) {
                SIDEBAR_LOGIN.classList.add('sidebar-abierto');
                SIDEBAR_LOGIN.classList.remove('sidebar-cerrado');
                if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.remove('hidden');
            }
        }, 300);
    });
}

// GESTIÓN DEL BUSCADOR
if (DISPARADOR_BUSQUEDA) {
    DISPARADOR_BUSQUEDA.addEventListener('click', (e) => {
        e.stopPropagation();
        if (CONTENEDOR_BUSQUEDA.classList.contains('hidden')) {
            CONTENEDOR_BUSQUEDA.classList.remove('hidden');
            setTimeout(() => INPUT_BUSQUEDA && INPUT_BUSQUEDA.focus(), 100);
        } else {
            CONTENEDOR_BUSQUEDA.classList.add('hidden');
        }
    });
}

// CERRAR BUSCADOR AL CLICAR FUERA
document.addEventListener('click', (e) => {
    if (CONTENEDOR_BUSQUEDA && !CONTENEDOR_BUSQUEDA.contains(e.target) && e.target !== DISPARADOR_BUSQUEDA) {
        CONTENEDOR_BUSQUEDA.classList.add('hidden');
    }
});

if (INPUT_BUSQUEDA) {
    INPUT_BUSQUEDA.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const valor = INPUT_BUSQUEDA.value.trim();
            window.location.href = valor ? `index.php?busqueda=${encodeURIComponent(valor)}` : 'index.php';
        }
    });
}

// LÓGICA DEL CARRITO DINÁMICO
async function cargarCarrito() {
    if (!CONTENEDOR_ITEMS_CARRITO) return;

    CONTENEDOR_ITEMS_CARRITO.innerHTML = '<p class="text-[10px] uppercase tracking-widest text-gray-400 text-center py-10">Actualizando cesta...</p>';

    try {
        const respuesta = await fetch('../modelos/carrito/obtener-carrito.php');
        const datos = await respuesta.json();

        if (datos.success && datos.items.length > 0) {
            CONTENEDOR_ITEMS_CARRITO.innerHTML = datos.items.map(item => `
                <div class="flex gap-4 group relative">
                    <div class="w-20 aspect-[3/4] bg-gray-50 overflow-hidden rounded-md">
                        <img src="../${item.imagen}" alt="${item.nombre}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 flex flex-col justify-between py-1">
                        <div>
                            <div class="flex justify-between items-start">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-fashion-black pr-4">${item.nombre}</h4>
                                <button onclick="eliminarDelCarrito('${item.key}')" class="text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="ph ph-trash text-sm"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase">Talla: ${item.talla} | Cant: ${item.cantidad}</p>
                        </div>
                        <p class="text-xs font-medium">${item.precio} €</p>
                    </div>
                </div>
            `).join('');

            if (SUBTOTAL_CARRITO) SUBTOTAL_CARRITO.textContent = datos.subtotal_f;

            if (BOTON_FINALIZAR_COMPRA) {
                BOTON_FINALIZAR_COMPRA.classList.replace('bg-gray-200', 'bg-fashion-black');
                BOTON_FINALIZAR_COMPRA.classList.replace('text-gray-400', 'text-white');
                BOTON_FINALIZAR_COMPRA.classList.remove('cursor-not-allowed', 'pointer-events-none');
                BOTON_FINALIZAR_COMPRA.classList.add('hover:bg-fashion-accent');
            }
        } else {
            CONTENEDOR_ITEMS_CARRITO.innerHTML = `
                <div class="text-center py-20 space-y-4">
                    <i class="ph ph-handbag text-5xl text-gray-200"></i>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest leading-loose">Tu cesta está vacía</p>
                </div>
            `;
            if (SUBTOTAL_CARRITO) SUBTOTAL_CARRITO.textContent = '0,00 €';

            if (BOTON_FINALIZAR_COMPRA) {
                BOTON_FINALIZAR_COMPRA.classList.replace('bg-fashion-black', 'bg-gray-200');
                BOTON_FINALIZAR_COMPRA.classList.replace('text-white', 'text-gray-400');
                BOTON_FINALIZAR_COMPRA.classList.add('cursor-not-allowed', 'pointer-events-none');
                BOTON_FINALIZAR_COMPRA.classList.remove('hover:bg-fashion-accent');
            }
        }
    } catch (error) {
        console.error('Error al cargar el carrito:', error);
        CONTENEDOR_ITEMS_CARRITO.innerHTML = '<p class="text-[10px] text-red-500 text-center py-10 uppercase tracking-widest">Error de conexión</p>';
    }
}

async function eliminarDelCarrito(clave) {
    try {
        const respuesta = await fetch('../modelos/carrito/eliminar-item-carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ key: clave })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            cargarCarrito();
            const contador = document.getElementById('contador-carrito');
            if (contador) {
                contador.textContent = resultado.total_items;
                contador.classList.toggle('hidden', resultado.total_items === 0);
            }
        }
    } catch (error) {
        console.error('Error al eliminar del carrito:', error);
    }
}

// --- AGREGAR RÁPIDO AL CARRITO (CATÁLOGO) ---
function seleccionarTallaRapida(productoId, talla, btn) {
    const tarjetaProducto = btn.closest('.group');
    if (!tarjetaProducto) return;

    tarjetaProducto.querySelectorAll('.quick-size-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-fashion-black', 'border-white');
        b.classList.add('border-white/30', 'text-white');
    });

    btn.classList.replace('border-white/30', 'bg-white');
    btn.classList.replace('text-white', 'text-fashion-black');
    btn.classList.add('border-white');

    const btnAñadir = document.getElementById(`quick-add-btn-${productoId}`);
    if (btnAñadir) {
        btnAñadir.setAttribute('data-selected-size', talla);
        btnAñadir.textContent = `Añadir talla ${talla}`;
    }
}

async function añadirAlCarritoRapido(productoId) {
    const btnAñadir = document.getElementById(`quick-add-btn-${productoId}`);
    if (!btnAñadir) return;

    const talla = btnAñadir.getAttribute('data-selected-size');

    if (!talla) {
        btnAñadir.textContent = 'Selecciona talla';
        btnAñadir.classList.add('bg-red-500', 'text-white');
        setTimeout(() => {
            btnAñadir.textContent = 'Añadir a la cesta';
            btnAñadir.classList.remove('bg-red-500', 'text-white');
        }, 1500);
        return;
    }

    const textoOriginal = btnAñadir.textContent;
    btnAñadir.disabled = true;
    btnAñadir.textContent = 'Añadiendo...';

    try {
        const fd = new FormData();
        fd.append('producto_id', productoId);
        fd.append('talla', talla);
        fd.append('cantidad', 1);

        const respuesta = await fetch('../modelos/carrito/agregar-carrito.php', {
            method: 'POST',
            body: fd
        });

        const res = await respuesta.json();

        if (res.success) {
            const contador = document.getElementById('contador-carrito');
            if (contador) {
                contador.textContent = res.total_items;
                contador.classList.remove('hidden');
            }

            btnAñadir.textContent = '¡Añadido!';
            btnAñadir.classList.replace('bg-white', 'bg-green-600');
            btnAñadir.classList.replace('text-fashion-black', 'text-white');

            setTimeout(() => ICONO_CARRITO && ICONO_CARRITO.click(), 500);

            setTimeout(() => {
                btnAñadir.textContent = 'Añadir a la cesta';
                btnAñadir.classList.replace('bg-green-600', 'bg-white');
                btnAñadir.classList.replace('text-white', 'text-fashion-black');
                btnAñadir.disabled = false;
                btnAñadir.removeAttribute('data-selected-size');
            }, 2000);
        } else {
            alert('Error: ' + res.message);
            btnAñadir.textContent = 'Error';
            btnAñadir.disabled = false;
        }
    } catch (e) {
        console.error(e);
        btnAñadir.textContent = 'Error de conexión';
        btnAñadir.disabled = false;
    }
}
