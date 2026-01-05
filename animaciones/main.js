const BOTON_LOGIN = document.getElementById('btn-login');
const DISPARADOR_BUSQUEDA = document.getElementById('disparador-busqueda');
const CONTENEDOR_BUSQUEDA = document.getElementById('contenedor-busqueda');
const INPUT_BUSQUEDA = document.getElementById('input-busqueda');
const SIDEBAR_LOGIN = document.getElementById('sidebar-login');
const CERRAR_LOGIN = document.getElementById('cerrar-login');

// Elementos del Carrito
const ICONO_CARRITO = document.getElementById('icono-carrito');
const SIDEBAR_CARRITO = document.getElementById('sidebar-carrito');
const CERRAR_CARRITO = document.getElementById('cerrar-carrito');
const CONTINUAR_COMPRANDO = document.getElementById('continuar-comprando');
const CAPA_SUPERPUESTA = document.getElementById('capa-superpuesta');
const CONTENEDOR_ITEMS_CARRITO = document.getElementById('contenedor-items-carrito');
const SUB_TOTAL_CARRITO = document.getElementById('subtotal-carrito');
const BOTON_FINALIZAR_COMPRA = document.getElementById('btn-finalizar-compra');

window.addEventListener('scroll', function () {
    const barraSuperior = document.getElementById('barra-superior');
    const cabeceraPrincipal = document.getElementById('cabecera-principal');

    if (window.scrollY > 30) {
        // Ocultar barra superior
        barraSuperior.style.height = '0';
        barraSuperior.style.padding = '0';
        barraSuperior.style.opacity = '0';
        barraSuperior.style.overflow = 'hidden';
        cabeceraPrincipal.style.backgroundColor = 'white';
    } else {
        // Mostrar barra superior
        barraSuperior.style.height = '';
        barraSuperior.style.padding = '';
        barraSuperior.style.opacity = '1';
        barraSuperior.style.overflow = '';
    }
});

// Función para cerrar todos los sidebars
function cerrarSidebars() {
    [SIDEBAR_LOGIN, SIDEBAR_CARRITO].forEach(sidebar => {
        if (sidebar) {
            sidebar.classList.remove('sidebar-abierto');
            sidebar.classList.add('sidebar-cerrado');
        }
    });
    if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.add('hidden');
    if (BOTON_LOGIN) BOTON_LOGIN.style.color = 'black';
}

// EVENTOS DE LOGIN
if (BOTON_LOGIN) {
    BOTON_LOGIN.addEventListener('click', function () {
        if (SIDEBAR_LOGIN.classList.contains('sidebar-abierto')) {
            cerrarSidebars();
        } else {
            cerrarSidebars();
            SIDEBAR_LOGIN.classList.add('sidebar-abierto');
            SIDEBAR_LOGIN.classList.remove('sidebar-cerrado');
            if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.classList.remove('hidden');
            BOTON_LOGIN.style.color = ' rgb(212 175 55 / var(--tw-text-opacity, 1))';
        }
    });
}

// EVENTOS DE BÚSQUEDA
if (DISPARADOR_BUSQUEDA) {
    DISPARADOR_BUSQUEDA.addEventListener('click', function (e) {
        e.stopPropagation();
        if (CONTENEDOR_BUSQUEDA.classList.contains('hidden')) {
            CONTENEDOR_BUSQUEDA.classList.remove('hidden');
            setTimeout(() => {
                INPUT_BUSQUEDA.focus();
            }, 100);
        } else {
            CONTENEDOR_BUSQUEDA.classList.add('hidden');
        }
    });
}

// CERRAR BUSCADOR AL PULSAR FUERA DE LA BARRA
document.addEventListener('click', function (e) {
    if (CONTENEDOR_BUSQUEDA && !CONTENEDOR_BUSQUEDA.contains(e.target)) {
        CONTENEDOR_BUSQUEDA.classList.add('hidden');
    }
});

// Evitar que clicks dentro del buscador lo cierren
if (CONTENEDOR_BUSQUEDA) {
    CONTENEDOR_BUSQUEDA.addEventListener('click', (e) => e.stopPropagation());
}

if (INPUT_BUSQUEDA) {
    INPUT_BUSQUEDA.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const valor = INPUT_BUSQUEDA.value.trim();
            if (valor) {
                window.location.href = `index.php?busqueda=${encodeURIComponent(valor)}`;
            } else {
                window.location.href = 'index.php';
            }
        }
    });
}

if (CERRAR_LOGIN) {
    CERRAR_LOGIN.addEventListener('click', cerrarSidebars);
}

// EVENTOS DE CARRITO
if (ICONO_CARRITO) {
    ICONO_CARRITO.addEventListener('click', function () {
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

if (CERRAR_CARRITO) CERRAR_CARRITO.addEventListener('click', cerrarSidebars);
if (CONTINUAR_COMPRANDO) CONTINUAR_COMPRANDO.addEventListener('click', cerrarSidebars);
if (CAPA_SUPERPUESTA) CAPA_SUPERPUESTA.addEventListener('click', cerrarSidebars);

// Función para cargar el carrito dinámicamente
async function cargarCarrito() {
    if (!CONTENEDOR_ITEMS_CARRITO) return;

    CONTENEDOR_ITEMS_CARRITO.innerHTML = '<p class="text-sm text-gray-500 text-center py-10">Actualizando cesta...</p>';

    try {
        const respuesta = await fetch('../modelos/carrito/obtener-carrito.php');
        const datos = await respuesta.json();

        if (datos.success && datos.items.length > 0) {
            let html = '';
            datos.items.forEach(item => {
                html += `
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
                `;
            });
            CONTENEDOR_ITEMS_CARRITO.innerHTML = html;
            if (SUBTOTAL_CARRITO) SUBTOTAL_CARRITO.textContent = datos.subtotal_f;

            // Habilitar botón de pago
            if (BTN_FINALIZAR_COMPRA) {
                BTN_FINALIZAR_COMPRA.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                BTN_FINALIZAR_COMPRA.classList.add('bg-fashion-black', 'text-white', 'hover:bg-fashion-accent');
            }
        } else {
            CONTENEDOR_ITEMS_CARRITO.innerHTML = `
                <div class="text-center py-20 space-y-4">
                    <i class="ph ph-handbag text-5xl text-gray-200"></i>
                    <p class="text-sm text-gray-500 uppercase tracking-widest leading-loose">Tu cesta está vacía</p>
                </div>
            `;
            if (SUBTOTAL_CARRITO) SUBTOTAL_CARRITO.textContent = '0,00 €';

            // Deshabilitar botón de pago
            if (BTN_FINALIZAR_COMPRA) {
                BTN_FINALIZAR_COMPRA.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                BTN_FINALIZAR_COMPRA.classList.remove('bg-fashion-black', 'text-white', 'hover:bg-fashion-accent');
            }
        }
    } catch (error) {
        console.error('Error al cargar el carrito:', error);
        CONTENEDOR_ITEMS_CARRITO.innerHTML = '<p class="text-xs text-red-500 text-center py-10 uppercase tracking-widest">Error al conectar con la cesta</p>';
    }
}

// Función para eliminar un item del carrito
async function eliminarDelCarrito(clave) {
    try {
        const respuesta = await fetch('../modelos/carrito/eliminar-item-carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ key: clave })
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            // Recargar el carrito para mostrar cambios
            cargarCarrito();

            // Actualizar contador en la cabecera
            const contador = document.getElementById('contador-carrito');
            if (contador) {
                contador.textContent = resultado.total_items;
                if (resultado.total_items === 0) {
                    contador.classList.add('hidden');
                }
            }
        } else {
            alert('Error: ' + resultado.error);
        }
    } catch (error) {
        console.error('Error al eliminar del carrito:', error);
    }
}
// --- QUICK ADD TO CART (CATALOG) ---

function seleccionarTallaRapida(productoId, talla, btn) {
    // Buscar todos los botones de talla del mismo producto
    const tarjetaProducto = btn.closest('.group');
    const todosLosBtns = tarjetaProducto.querySelectorAll('.quick-size-btn');

    // Quitar activo de todos
    todosLosBtns.forEach(b => {
        b.classList.remove('bg-white', 'text-fashion-black', 'border-white');
        b.classList.add('border-white/30', 'text-white');
    });

    // Activar el seleccionado
    btn.classList.remove('border-white/30', 'text-white');
    btn.classList.add('bg-white', 'text-fashion-black', 'border-white');

    // Guardar la talla en el botón de añadir
    const btnAñadir = document.getElementById(`quick-add-btn-${productoId}`);
    if (btnAñadir) {
        btnAñadir.setAttribute('data-selected-size', talla);
        btnAñadir.textContent = `Añadir talla ${talla}`;
    }
}

async function añadirAlCarritoRapido(productoId) {
    const btnAñadir = document.getElementById(`quick-add-btn-${productoId}`);
    const talla = btnAñadir.getAttribute('data-selected-size');

    if (!talla) {
        // Notificación visual si no hay talla
        btnAñadir.textContent = 'Selecciona talla';
        btnAñadir.classList.add('bg-red-500', 'text-white');
        setTimeout(() => {
            btnAñadir.textContent = 'Añadir a la cesta';
            btnAñadir.classList.remove('bg-red-500', 'text-white');
        }, 1500);
        return;
    }

    // Feedback visual de carga
    const textoOriginal = btnAñadir.textContent;
    btnAñadir.disabled = true;
    btnAñadir.textContent = 'Añadiendo...';

    try {
        const datosFormulario = new FormData();
        datosFormulario.append('producto_id', productoId);
        datosFormulario.append('talla', talla);
        datosFormulario.append('cantidad', 1);

        const respuesta = await fetch('../modelos/carrito/agregar-carrito.php', {
            method: 'POST',
            body: datosFormulario
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            // Actualizar badge del carrito si existe
            const contador = document.getElementById('contador-carrito');
            if (contador) {
                contador.textContent = resultado.total_items;
                contador.classList.remove('hidden');
            }

            // Éxito: Feedback visual
            btnAñadir.textContent = '¡Añadido!';
            btnAñadir.classList.remove('bg-white', 'text-fashion-black');
            btnAñadir.classList.add('bg-green-600', 'text-white');

            // Reabrir el carrito para mostrar el item (opcional pero común)
            setTimeout(() => {
                if (typeof ICONO_CARRITO !== 'undefined' && ICONO_CARRITO) ICONO_CARRITO.click();
            }, 500);

            setTimeout(() => {
                btnAñadir.textContent = textoOriginal;
                btnAñadir.classList.remove('bg-green-600', 'text-white');
                btnAñadir.classList.add('bg-white', 'text-fashion-black');
                btnAñadir.disabled = false;
            }, 2000);
        } else {
            alert('Error: ' + resultado.message);
            btnAñadir.textContent = textoOriginal;
            btnAñadir.disabled = false;
        }
    } catch (error) {
        console.error('Error en Quick Add:', error);
        alert('Error al conectar con el servidor.');
        btnAñadir.textContent = textoOriginal;
        btnAñadir.disabled = false;
    }
}
