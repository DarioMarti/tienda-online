


// GESTIÓN DE LAS PESTAÑAS
function cambiarPestaña(idPestaña, actualizarUrl = true) {

    // OCULTAR TODAS LAS SECCIONES
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });

    // MOSTRAR LA SECCIÓN SELECCIONADA
    const seccionSeleccionada = document.getElementById(`seccion-${idPestaña}`);
    if (seccionSeleccionada) {
        seccionSeleccionada.classList.remove('hidden');
        seccionSeleccionada.classList.add('block');

        localStorage.setItem('activeAdminTab', idPestaña);

        if (actualizarUrl) {
            const url = new URL(window.location);
            url.searchParams.set('tab', idPestaña);
            window.history.replaceState({}, '', url);
        }
    }

    // ACTUALIZAR ESTADO ACTIVO EN EL SIDEBAR
    document.querySelectorAll('.nav-item').forEach(el => {
        el.classList.remove('bg-fashion-gray', 'text-fashion-black');
        el.classList.add('text-gray-500', 'hover:bg-fashion-gray', 'hover:text-fashion-black');
    });

    const botonActivo = document.querySelector(`button[data-tab="${idPestaña}"]`);
    if (botonActivo) {
        botonActivo.classList.remove('text-gray-500', 'hover:bg-fashion-gray', 'hover:text-fashion-black');
        botonActivo.classList.add('bg-fashion-gray', 'text-fashion-black');
    }
}


// GESTIONAR EL MODAL DE USUARIOS
const modalUsuario = document.getElementById('modal-usuario');
const formularioUsuario = document.getElementById('formulario-usuario');
const tituloModal = document.getElementById('titulo-modal');
const accionFormulario = document.getElementById('accion-formulario');
const inputIdUsuario = document.getElementById('id-usuario');
const grupoContraseña = document.getElementById('grupo-password');
const pistaContraseña = document.getElementById('pista-password');
const selectorRol = document.getElementById('rol');

function abrirModalUsuario() {
    modalUsuario.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // PREVENIR SCROLL

    // RESETEAR FORMULARIO PARA "CREAR"
    formularioUsuario.reset();
    tituloModal.textContent = 'Nuevo Usuario';
    accionFormulario.value = 'create';
    inputIdUsuario.value = '';
    grupoContraseña.style.display = 'block'; // MOSTRAR CAMPO CONTRASEÑA
    pistaContraseña.style.display = 'none'; // Ocultar pista de "dejar en blanco"

    // HABILITAR SELECTOR DE ROL POR DEFECTO
    selectorRol.disabled = false;
    // ELIMINAR INPUT HIDDEN DE ROL SI EXISTE
    const rolOculto = formularioUsuario.querySelector('input[name="rol"][type="hidden"]');
    if (rolOculto) rolOculto.remove();
}

function cerrarModalUsuario() {
    modalUsuario.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editarUsuario(usuario) {
    abrirModalUsuario();

    tituloModal.textContent = 'Editar Usuario';
    accionFormulario.value = 'update';
    inputIdUsuario.value = usuario.id;

    // RELLENAR DATOS
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('apellidos').value = usuario.apellidos;
    document.getElementById('email').value = usuario.email;
    document.getElementById('rol').value = usuario.rol;
    document.getElementById('activo').value = usuario.activo;

    // AJUSTES PARA EDICIÓN
    pistaContraseña.style.display = 'block';

    // PROTECCIÓN: SI EL USUARIO INTENTA EDITARSE A SÍ MISMO
    if (usuario.id == idUsuarioActual) {
        selectorRol.disabled = true; // DESHABILITAR CAMBIO DE ROL

        // Añadir input hidden para enviar el valor del rol (ya que disabled no se envía)
        let rolOculto = formularioUsuario.querySelector('input[name="rol"][type="hidden"]');
        if (!rolOculto) {
            rolOculto = document.createElement('input');
            rolOculto.type = 'hidden';
            rolOculto.name = 'rol';
            formularioUsuario.appendChild(rolOculto);
        }
        rolOculto.value = usuario.rol;
    } else {
        selectorRol.disabled = false;
        const rolOculto = formularioUsuario.querySelector('input[name="rol"][type="hidden"]');
        if (rolOculto) rolOculto.remove();
    }
}

// CERRAR MODAL AL HACER CLIC FUERA
modalUsuario.addEventListener('click', (e) => {
    if (e.target === modalUsuario) {
        cerrarModalUsuario();
    }
});

// MANEJAR ENVÍOS DE FORMULARIOS CON AJAX (DELEGACIÓN DE EVENTOS PARA MAYOR ROBUSTEZ)
document.addEventListener('submit', async (e) => {
    const form = e.target;

    // LISTA DE FORMULARIOS QUE MANEJAMOS POR AJAX
    const formulariosAjax = ['formulario-usuario', 'formulario-producto', 'formulario-categoria', 'formulario-pedido'];

    if (formulariosAjax.includes(form.id)) {
        e.preventDefault();
        const botonEnvio = form.querySelector('button[type="submit"]');
        if (botonEnvio) {
            botonEnvio.disabled = true;
            botonEnvio.dataset.originalText = botonEnvio.textContent;
            botonEnvio.textContent = 'Guardando...';
        }

        console.log(`[AJAX] Enviando ${form.id}...`);
        const formData = new FormData(form);

        for (let [key, value] of formData.entries()) {
            console.log(`[AJAX Payload] ${key}:`, value instanceof File ? `Archivo: ${value.name}` : value);
        }

        // LÓGICA ESPECÍFICA PARA PRODUCTOS: RECOGER TALLAS
        if (form.id === 'formulario-producto') {
            const tallas = [];
            const filas = form.querySelectorAll('.size-row');
            const tallasUnicas = new Set();
            let hayDuplicados = false;

            filas.forEach(fila => {
                const tallaInput = fila.querySelector('input[name="talla_input[]"]');
                const tallaRaw = tallaInput ? tallaInput.value.trim() : '';
                const talla = tallaRaw.toUpperCase();
                const stock = 0;

                if (tallaRaw) {
                    if (tallasUnicas.has(talla)) {
                        hayDuplicados = true;
                    }
                    tallasUnicas.add(talla);
                    tallas.push({ talla: tallaRaw, stock });
                }
            });

            if (hayDuplicados) {
                mostrarNotificacion('error', 'No puedes repetir la misma talla dos veces.');
                if (botonEnvio) {
                    botonEnvio.disabled = false;
                    botonEnvio.textContent = botonEnvio.dataset.originalText;
                }
                return;
            }

            if (tallas.length === 0) {
                mostrarNotificacion('error', 'Debes introducir al menos una talla obligatoriamente.');
                if (botonEnvio) {
                    botonEnvio.disabled = false;
                    botonEnvio.textContent = botonEnvio.dataset.originalText;
                }
                return;
            }

            formData.append('tallas_stock', JSON.stringify(tallas));
            console.log("[AJAX Payload] tallas_stock (JSON):", JSON.stringify(tallas));
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
                if (form.id === 'formulario-usuario') cerrarModalUsuario();
                if (form.id === 'formulario-producto') cerrarModalProducto();
                if (form.id === 'formulario-categoria') cerrarModalCategoria();
                if (form.id === 'formulario-pedido') cerrarModalPedido();

                mostrarNotificacion('success', result.message);
                window.pendingReload = true;
            } else {
                mostrarNotificacion('error', result.message);
            }
        } catch (error) {
            console.error('Error en proceso AJAX:', error);
            mostrarNotificacion('error', 'Error: ' + error.message);
        } finally {
            if (botonEnvio) {
                botonEnvio.disabled = false;
                botonEnvio.textContent = botonEnvio.dataset.originalText;
            }
        }
    }
});

// GESTIONAR EL MODAL DE ELIMINACIÓN
const modalEliminarAdmin = document.getElementById('modal-eliminacion');
const mensajeEliminacion = document.getElementById('mensaje-eliminacion');
let urlObjetivoEliminacion = '';

function abrirModalEliminar(url, mensaje, textoBtn = 'Eliminar', colorBtn = 'bg-red-600') {
    if (!modalEliminarAdmin) return;
    urlObjetivoEliminacion = url;
    const elMensaje = document.getElementById('mensaje-eliminacion');
    if (elMensaje) elMensaje.textContent = mensaje;

    const botonConfirmar = document.getElementById('boton-confirmar-eliminacion');
    if (botonConfirmar) {
        botonConfirmar.textContent = textoBtn;

        // LIMPIAR TODAS LAS CLASES DE COLOR POSIBLES (INCLUYENDO LAS DE TAILWIND ESTANDAR Y PERSONALIZADAS)
        botonConfirmar.className = botonConfirmar.className.replace(/\bbg-\S+/g, '').replace(/\bhover:bg-\S+/g, '');

        // AÑADIR LAS CLASES BASE NECESARIAS (QUE PERDIMOS CON EL REPLACE ANTERIOR SI ESTABAN AHÍ)
        botonConfirmar.classList.add('flex-1', 'text-white', 'py-2', 'px-4', 'text-[10px]', 'uppercase', 'tracking-widest', 'font-semibold', 'transition-colors', 'rounded');

        // APLICAR EL COLOR ESPECÍFICO
        if (colorBtn === 'bg-green-600' || colorBtn === 'bg-emerald-600') {
            botonConfirmar.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        } else if (colorBtn === 'bg-red-600') {
            botonConfirmar.classList.add('bg-red-600', 'hover:bg-red-700');
        } else {
            botonConfirmar.classList.add('bg-fashion-black', 'hover:bg-fashion-accent');
        }
    }

    modalEliminarAdmin.classList.remove('hidden');
}

function cerrarModalEliminar() {
    if (modalEliminarAdmin) {
        modalEliminarAdmin.classList.add('hidden');
        urlObjetivoEliminacion = '';
    }
}

function confirmarEliminacion() {
    if (urlObjetivoEliminacion) {
        window.location.href = urlObjetivoEliminacion;
    }
}

function eliminarUsuario(id, nombre) {
    const url = `../modelos/usuarios/admin-eliminar-usuario.php?id=${id}`;
    const msg = `¿Deseas DESACTIVAR al usuario "${nombre}"? Podrás reactivarlo más tarde.`;
    abrirModalEliminar(url, msg, 'Desactivar', 'bg-red-600');
}

function activarUsuario(id, nombre) {
    const url = `../modelos/usuarios/activar-usuario.php?id=${id}`;
    const msg = `¿Deseas REACTIVAR al usuario "${nombre}"?`;
    abrirModalEliminar(url, msg, 'Reactivar', 'bg-emerald-600');
}

function eliminarProducto(id, nombre) {
    const url = `../modelos/productos/eliminar-producto.php?id=${id}`;
    const msg = `¿Deseas DESACTIVAR el producto "${nombre}"? Dejará de ser visible en la tienda.`;
    abrirModalEliminar(url, msg, 'Desactivar', 'bg-red-600');
}

function activarProducto(id, nombre) {
    const url = `../modelos/productos/activar-producto.php?id=${id}`;
    const msg = `¿Deseas REACTIVAR el producto "${nombre}"? Volverá a estar visible en la tienda.`;
    abrirModalEliminar(url, msg, 'Reactivar', 'bg-emerald-600');
}

function eliminarCategoria(id, nombre) {
    const url = `../modelos/categorias/eliminar-categoria.php?id=${id}`;
    const msg = `¿Deseas DESACTIVAR la categoría "${nombre}"?`;
    abrirModalEliminar(url, msg, 'Desactivar', 'bg-red-600');
}

function activarCategoria(id, nombre) {
    const url = `../modelos/categorias/activar-categoria.php?id=${id}`;
    const msg = `¿Deseas REACTIVAR la categoría "${nombre}"?`;
    abrirModalEliminar(url, msg, 'Reactivar', 'bg-emerald-600');
}

// GESTIÓN DEL MODAL DE PEDIDOS
const modalPedido = document.getElementById('modal-pedido');
const formularioPedido = document.getElementById('formulario-pedido');
const tituloModalPedido = document.getElementById('titulo-modal-pedido');
const inputIdPedido = document.getElementById('id-pedido');

function abrirModalPedido() {
    modalPedido.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // RESETEAR FORMULARIO
    formularioPedido.reset();
    tituloModalPedido.textContent = 'Nuevo Pedido';
    formularioPedido.action = '../modelos/pedidos/crear-pedido.php';
    inputIdPedido.value = '';

    // LIMPIAR PRODUCTOS Y AÑADIR UNA FILA VACÍA
    document.getElementById('constructor-items-pedido').innerHTML = '';
    añadirFilaProducto();
    document.getElementById('coste-total-pedido').value = '0.00';
    window.editingOrderOriginalItems = null; // LIMPIAR PARA NUEVO PEDIDO
    validarStock();
}



function cerrarModalPedido() {
    modalPedido.classList.add('hidden');
    document.getElementById('aviso-stock-pedido').classList.add('hidden');
    document.getElementById('lista-avisos-stock').innerHTML = '';
    document.body.style.overflow = 'auto';
}

function editarPedido(pedido) {
    abrirModalPedido();
    tituloModalPedido.textContent = 'Editar Pedido';
    formularioPedido.action = '../modelos/pedidos/modificar-pedido.php';
    inputIdPedido.value = pedido.id;

    document.getElementById('email-usuario-pedido').value = pedido.usuario_email || '';

    document.getElementById('coste-total-pedido').value = pedido.coste_total;
    document.getElementById('nombre-destinatario-pedido').value = pedido.nombre_destinatario;
    document.getElementById('direccion-envio-pedido').value = pedido.direccion_envio;
    document.getElementById('ciudad-pedido').value = pedido.ciudad;
    document.getElementById('provincia-pedido').value = pedido.provincia;
    document.getElementById('estado-pedido').value = pedido.estado;

    // CARGAR PRODUCTOS DEL PEDIDO
    const contenedorItems = document.getElementById('constructor-items-pedido');
    contenedorItems.innerHTML = ''; // LIMPIAR

    fetch(`../modelos/pedidos/obtener-detalle-pedido.php?id=${pedido.id}`)
        .then(res => res.json())
        .then(resultado => {
            if (resultado.success) {
                window.editingOrderOriginalItems = resultado.items;

                resultado.items.forEach(item => {
                    añadirFilaProducto(item);
                });
                calcularTotalPedido();
                validarStock();
            }
        });
}


function eliminarPedido(id) {
    const url = `../modelos/pedidos/eliminar-pedido.php?id=${id}&redirect=true`;
    const msg = `¿Deseas eliminar el pedido #${id}? Esta acción no se puede deshacer.`;
    abrirModalEliminar(url, msg);
}



// GESTIÓN DEL MODAL DE DETALLES DE PEDIDO
const modalDetallesPedido = document.getElementById('modal-detalles-pedido');

async function verDetallesPedido(id) {
    if (!modalDetallesPedido) return;

    try {
        const respuesta = await fetch(`../modelos/pedidos/obtener-detalle-pedido.php?id=${id}`);
        const resultado = await respuesta.json();

        if (resultado.success) {
            const p = resultado.pedido;
            const items = resultado.items;

            // RELLENAR CABECERA
            document.getElementById('det-id-pedido').textContent = `#${p.id}`;
            document.getElementById('det-fecha-pedido').textContent = new Date(p.fecha).toLocaleString();

            // RELLENAR CLIENTE
            document.getElementById('det-nombre-cliente').textContent = p.nombre_destinatario;
            document.getElementById('det-email-cliente').textContent = p.usuario_email || 'Venta Anónima';

            // BADGE DE ESTADO
            const badge = document.getElementById('det-badge-estado');
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

            // DIRECCIÓN
            document.getElementById('det-direccion-envio').textContent = p.direccion_envio;
            document.getElementById('det-ubicacion-envio').textContent = `${p.ciudad}, ${p.provincia}`;

            // ITEMS
            const listaDetallesItems = document.getElementById('det-lista-items');
            listaDetallesItems.innerHTML = '';
            items.forEach(item => {
                const total = (item.cantidad * item.precio_unitario).toFixed(2);
                listaDetallesItems.innerHTML += `
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

            // TOTAL
            document.getElementById('det-total-pedido').textContent = `${parseFloat(p.coste_total).toFixed(2)} €`;

            // MOSTRAR MODAL
            modalDetallesPedido.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            mostrarNotificacion('error', resultado.message);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error al cargar detalles del pedido');
    }
}

function cerrarModalDetallesPedido() {
    if (modalDetallesPedido) {
        modalDetallesPedido.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}


// CERRAR MODAL AL HACER CLIC FUERA
if (modalPedido) {
    modalPedido.addEventListener('click', (e) => {
        if (e.target === modalPedido) cerrarModalPedido();
    });
}

if (modalDetallesPedido) {
    modalDetallesPedido.addEventListener('click', (e) => {
        if (e.target === modalDetallesPedido) cerrarModalDetallesPedido();
    });
}



// LÓGICA DE SELECCIÓN DE PRODUCTOS EN PEDIDOS
function añadirFilaProducto(itemData = null) {
    const contenedor = document.getElementById('constructor-items-pedido');
    if (!contenedor) return;

    const rowId = Date.now() + Math.random();
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50 transition-colors order-item-row';
    tr.id = `row-${rowId}`;

    let options = '<option value="">Seleccionar Producto...</option>';
    todosLosProductos.forEach(p => {
        const selected = (itemData && itemData.producto_id == p.id) ? 'selected' : '';
        const statusLabel = parseInt(p.activo) === 0 ? ' (Inactivo)' : '';
        options += `<option value="${p.id}" data-precio="${p.precio}" ${selected}>${p.nombre}${statusLabel}</option>`;
    });

    tr.innerHTML = `
        <td class="px-4 py-3">
            <select name="producto_id[]" required onchange="actualizarSubtotalFila(this)"
                class="w-full text-xs border border-gray-200 rounded p-2 focus:outline-none focus:border-fashion-black">
                ${options}
            </select>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center border border-gray-300 rounded overflow-hidden w-32 bg-white shadow-sm quantity-container">
                <button type="button" onclick="ajustarCantidad(this, -1)" 
                    class="w-10 h-9 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-800 transition-colors border-r border-gray-200 text-base font-bold shrink-0">-</button>
                <input type="number" name="cantidad[]" value="${itemData ? itemData.cantidad : 1}" min="1" step="1" required
                    oninput="actualizarSubtotalFila(this)"
                    class="w-full text-sm py-1 focus:outline-none text-center quantity-input bg-white text-gray-900 font-bold h-9 px-1">
                <button type="button" onclick="ajustarCantidad(this, 1)" 
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
            <button type="button" onclick="quitarFilaProducto(this)" class="text-gray-300 hover:text-red-500 transition-colors">
                <i class="ph ph-trash"></i>
            </button>
        </td>
    `;

    contenedor.appendChild(tr);
    // INICIALIZAR SUBTOTAL PARA ESTA FILA
    const select = tr.querySelector('select');
    actualizarSubtotalFila(select);
}

function quitarFilaProducto(btn) {
    const fila = btn.closest('tr');
    if (fila) {
        fila.remove();
        calcularTotalPedido();
        validarStock();
    }
}

function ajustarCantidad(btn, delta) {
    const contenedor = btn.closest('.quantity-container');
    const input = contenedor.querySelector('.quantity-input');
    let val = parseInt(input.value) || 0;
    val += delta;
    if (val < 1) val = 1;
    input.value = val;
    actualizarSubtotalFila(input);
}


function actualizarSubtotalFila(elemento) {
    const fila = elemento.closest('tr');
    if (!fila) return;

    const select = fila.querySelector('select');
    const inputCantidad = fila.querySelector('.quantity-input');
    const spanPrecioUnitario = fila.querySelector('.unit-price');
    const spanSubtotal = fila.querySelector('.subtotal');

    if (!select || !inputCantidad || !spanPrecioUnitario || !spanSubtotal) return;

    const opcionSeleccionada = select.options[select.selectedIndex];
    const precio = parseFloat(opcionSeleccionada.dataset.precio) || 0;
    const cantidad = parseInt(inputCantidad.value) || 0;

    const subtotal = precio * cantidad;

    spanPrecioUnitario.textContent = precio.toFixed(2);
    spanSubtotal.textContent = subtotal.toFixed(2);

    calcularTotalPedido();
    validarStock();
}


function validarStock() {
    let todoValido = true;
    const filas = document.querySelectorAll('.order-item-row');
    const botonEnvio = document.querySelector('#formulario-pedido button[type="submit"]');
    const avisoGlobal = document.getElementById('aviso-stock-pedido');
    const listaAvisos = document.getElementById('lista-avisos-stock');

    if (!avisoGlobal || !listaAvisos) return;

    listaAvisos.innerHTML = '';
    let errores = [];

    filas.forEach(fila => {
        const select = fila.querySelector('select');
        const inputCantidad = fila.querySelector('.quantity-input');
        const contenedorCantidad = fila.querySelector('.quantity-container');

        if (!select || !inputCantidad) return;

        const idP = select.value;
        const cantidad = parseInt(inputCantidad.value) || 0;

        if (idP) {
            const producto = typeof todosLosProductos !== 'undefined' ? todosLosProductos.find(p => p.id == idP) : null;
            if (producto) {
                let stockDisponible = parseInt(producto.stock) || 0;

                if (window.editingOrderOriginalItems) {
                    const itemOriginal = window.editingOrderOriginalItems.find(it => it.producto_id == idP);
                    if (itemOriginal) {
                        stockDisponible += parseInt(itemOriginal.cantidad) || 0;
                    }
                }

                if (cantidad > stockDisponible) {
                    todoValido = false;
                    if (contenedorCantidad) contenedorCantidad.classList.add('border-red-500', 'bg-red-50');
                    errores.push(`<strong>${producto.nombre}</strong>: Has solicitado ${cantidad} pero solo hay ${stockDisponible} disponibles.`);
                } else {
                    if (contenedorCantidad) contenedorCantidad.classList.remove('border-red-500', 'bg-red-50');
                }
            }
        }
    });

    if (errores.length > 0) {
        avisoGlobal.classList.remove('hidden');
        errores.forEach(err => {
            const li = document.createElement('li');
            li.innerHTML = err;
            listaAvisos.appendChild(li);
        });
    } else {
        avisoGlobal.classList.add('hidden');
    }

    if (botonEnvio) {
        if (!todoValido) {
            botonEnvio.disabled = true;
            botonEnvio.style.setProperty('background-color', '#9ca3af', 'important');
            botonEnvio.style.setProperty('opacity', '0.6', 'important');
            botonEnvio.style.setProperty('cursor', 'not-allowed', 'important');
            botonEnvio.classList.remove('hover:bg-fashion-accent');
        } else {
            botonEnvio.disabled = false;
            botonEnvio.style.backgroundColor = '';
            botonEnvio.style.opacity = '';
            botonEnvio.style.cursor = '';
            botonEnvio.classList.add('hover:bg-fashion-accent');
        }
    }
}







function calcularTotalPedido() {
    let total = 0;
    document.querySelectorAll('#constructor-items-pedido .subtotal').forEach(span => {
        total += parseFloat(span.textContent) || 0;
    });
    const inputTotal = document.getElementById('coste-total-pedido');
    if (inputTotal) inputTotal.value = total.toFixed(2);
}


// GESTIÓN DEL MODAL DE PRODUCTOS
const modalProducto = document.getElementById('modal-producto');
const formularioProducto = document.getElementById('formulario-producto');
const tituloModalProducto = document.getElementById('titulo-modal-producto');
const accionFormularioProducto = document.getElementById('accion-formulario-producto');
const inputIdProducto = document.getElementById('id-producto');
const zonaDrop = document.getElementById('zona-drop');
const inputArchivo = document.getElementById('imagen-producto');
const previsualizacionImagen = document.getElementById('previsualizacion-imagen');
const placeholderSubir = document.getElementById('placeholder-subida');
const capaCambiarImagen = document.getElementById('capa-cambio-imagen');

function abrirModalProducto() {
    modalProducto.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // RESETEAR FORMULARIO
    formularioProducto.reset();
    tituloModalProducto.textContent = 'Nuevo Producto';
    formularioProducto.setAttribute('action', '../modelos/productos/agregar-producto.php');
    accionFormularioProducto.value = 'create';
    inputIdProducto.value = '';

    // Resetear preview
    previsualizacionImagen.src = '#';
    previsualizacionImagen.classList.add('hidden');
    placeholderSubir.classList.remove('hidden');
    capaCambiarImagen.classList.add('hidden');

    // INICIALIZAR TALLAS
    const contenedorTallas = document.getElementById('contenedor-tallas');
    if (contenedorTallas) {
        contenedorTallas.innerHTML = ''; // LIMPIAR CONTENEDOR
        // PEQUEÑO TIMEOUT PARAASEGURAR QUE EL DOM ESTÁ LISTO SI HAY LAG
        setTimeout(() => {
            if (typeof añadirFilaTalla === 'function') {
                añadirFilaTalla();
            } else {
                console.error("añadirFilaTalla no está definida");
            }
        }, 10);
    }
}




// DELEGACIÓN DE EVENTOS GLOBAL A NIVEL DE DOCUMENTO
document.addEventListener('click', (e) => {
    console.log('Click detected on:', e.target);

    //AÑADIR TALLA
    const botónAñadirTalla = e.target.closest('#boton-añadir-talla');
    if (botónAñadirTalla) {
        console.log('Botón Añadir Talla clickeado');
        e.preventDefault();
        e.stopPropagation(); // Evitar propagación
        añadirFilaTalla();
        return;
    }

    //ELIMINAR TALLA
    const botonEliminarTalla = e.target.closest('.delete-size-btn');
    if (botonEliminarTalla) {
        e.preventDefault();
        e.stopPropagation();
        botonEliminarTalla.closest('.size-row').remove();
        return;
    }
});

// AÑADIR FILA
function añadirFilaTalla(talla = '') {
    console.log("añadirFilaTalla called");

    const contenedor = document.getElementById('contenedor-tallas');
    if (!contenedor) {
        console.error("ERROR CRÍTICO: No se encuentra el contenedor #sizes-container");
        return;
    }

    const div = document.createElement('div');
    div.className = 'flex gap-4 items-center size-row mb-2 relative';

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
        contenedor.appendChild(div);
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






function cerrarModalProducto() {
    modalProducto.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// DRAG & DROP LOGIC
if (zonaDrop) {
    zonaDrop.addEventListener('click', () => inputArchivo.click());

    zonaDrop.addEventListener('dragover', (e) => {
        e.preventDefault();
        zonaDrop.querySelector('#image-preview-container').classList.add('border-fashion-black', 'bg-gray-100');
    });

    zonaDrop.addEventListener('dragleave', (e) => {
        e.preventDefault();
        zonaDrop.querySelector('#image-preview-container').classList.remove('border-fashion-black', 'bg-gray-100');
    });

    zonaDrop.addEventListener('drop', (e) => {
        e.preventDefault();
        zonaDrop.querySelector('#image-preview-container').classList.remove('border-fashion-black', 'bg-gray-100');

        if (e.dataTransfer.files.length > 0) {
            inputArchivo.files = e.dataTransfer.files;
            previsualizarImagen(inputArchivo);
        }
    });
}

function previsualizarImagen(input) {
    const previsualizacionImagen = document.getElementById('previsualizacion-imagen');
    const placeholderSubir = document.getElementById('placeholder-subida');
    const capaCambiarImagen = document.getElementById('capa-cambio-imagen');

    if (input.files && input.files[0]) {
        const lector = new FileReader();

        lector.onload = function (e) {
            previsualizacionImagen.src = e.target.result;
            previsualizacionImagen.classList.remove('hidden');
            placeholderSubir.classList.add('hidden');
            capaCambiarImagen.classList.remove('hidden');
        }

        lector.readAsDataURL(input.files[0]);
    }
}

// CERRAR MODAL DE PRODUCTO AL HACER CLIC FUERA
if (modalProducto) {
    modalProducto.addEventListener('click', (e) => {
        if (e.target === modalProducto) {
            cerrarModalProducto();
        }
    });
}

// MANEJO DE ENVÍO AJAX

// AJUSTAR VALOR
function ajustarValor(idInput, paso) {
    const input = document.getElementById(idInput);
    let val = parseFloat(input.value) || 0;
    val += paso;
    if (val < 0) val = 0;

    if (paso % 1 !== 0) {
        input.value = val.toFixed(2);
    } else {
        input.value = Math.round(val);
    }
}

function editarProducto(producto) {
    modalProducto.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // RELLENAR FORMULARIO
    tituloModalProducto.textContent = 'Modificar Producto';
    formularioProducto.setAttribute('action', '../modelos/productos/modificar-producto.php');
    accionFormularioProducto.value = 'update';
    inputIdProducto.value = producto.id;

    document.getElementById('nombre-producto').value = producto.nombre;
    document.getElementById('precio-producto').value = producto.precio;
    document.getElementById('descuento-producto').value = producto.descuento || 0;
    document.getElementById('stock-producto').value = producto.stock;
    document.getElementById('id-categoria-producto').value = producto.categoria_id;
    document.getElementById('descripcion-producto').value = producto.descripcion;

    // CARGAR IMAGEN EN PREVIEW
    if (producto.imagen) {
        previsualizacionImagen.src = '../' + producto.imagen;
        previsualizacionImagen.classList.remove('hidden');
        placeholderSubir.classList.add('hidden');
        capaCambiarImagen.classList.remove('hidden');
    }

    // GESTIONAR TALLAS
    const contenedorTallas = document.getElementById('contenedor-tallas');
    if (contenedorTallas) {
        contenedorTallas.innerHTML = '';

        // FETCH PARA OBTENER TALLAS DEL PRODUCTO
        fetch(`../modelos/productos/obtener-tallas-producto.php?id=${producto.id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.tallas && data.tallas.length > 0) {
                    data.tallas.forEach(t => {
                        añadirFilaTalla(t.talla);
                    });
                } else {
                    // Si no tiene tallas, añadir una fila vacía
                    añadirFilaTalla();
                }
            })
            .catch(err => {
                console.error('Error al cargar tallas:', err);
                añadirFilaTalla();
            });
    }
}



// GESTIÓN DEL MODAL DE CATEGORÍAS
const modalCategoria = document.getElementById('modal-categoria');
const formularioCategoria = document.getElementById('formulario-categoria');
const tituloModalCategoria = document.getElementById('titulo-modal-categoria');
const accionFormularioCategoria = document.getElementById('accion-formulario-categoria');
const inputIdCategoria = document.getElementById('id-categoria');

function abrirModalCategoria() {
    modalCategoria.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // RESETEAR FORMULARIO
    formularioCategoria.reset();
    tituloModalCategoria.textContent = 'Nueva Categoría';
    // SET ACTION TO CREATE
    formularioCategoria.action = '../modelos/categorias/crear-categoria.php';

    inputIdCategoria.value = '';
}

function cerrarModalCategoria() {
    modalCategoria.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editarCategoria(categoria) {
    abrirModalCategoria();

    // CAMBIAR TÍTULO Y ACCIÓN
    tituloModalCategoria.textContent = 'Editar Categoría';
    // SET ACTION TO UPDATE
    formularioCategoria.action = '../modelos/categorias/modificar-categoria.php';

    inputIdCategoria.value = categoria.id;

    // RELLENAR DATOS
    document.getElementById('nombre-categoria').value = categoria.nombre;
    document.getElementById('descripcion-categoria').value = categoria.descripcion;
    document.getElementById('id-padre-categoria').value = categoria.categoria_padre_id || '';
}



function activarCategoria(id, nombre) {
    const url = `../modelos/categorias/activar-categoria.php?id=${id}`;
    const msg = `¿Deseas reactivar la categoría "${nombre}"?`;
    abrirModalEliminar(url, msg, 'Reactivar', 'bg-green-600');
}



function activarPedido(id) {
    const url = `../modelos/pedidos/activar-pedido.php?id=${id}&redirect=true`;
    const msg = `¿Deseas reactivar el pedido #${id}?`;
    abrirModalEliminar(url, msg, 'Reactivar', 'bg-green-600');
}

// CERRAR MODAL AL HACER CLIC FUERA
if (modalCategoria) {
    modalCategoria.addEventListener('click', (e) => {
        if (e.target === modalCategoria) {
            cerrarModalCategoria();
        }
    });
}

// GESTIÓN DEL MODAL DE NOTIFICACIONES
const modalNotificacion = document.getElementById('modal-notificacion');
const iconoNotificacion = document.getElementById('icono-notificacion');
const tituloNotificacion = document.getElementById('titulo-notificacion');
const mensajeNotificacion = document.getElementById('mensaje-notificacion');

function mostrarNotificacion(tipo, mensaje) {
    if (!modalNotificacion) return;

    modalNotificacion.classList.remove('hidden');

    if (tipo === 'success') {
        iconoNotificacion.innerHTML = '<i class="ph ph-check-circle text-green-500"></i>';
        tituloNotificacion.textContent = '¡Éxito!';
    } else {
        iconoNotificacion.innerHTML = '<i class="ph ph-warning-circle text-red-500"></i>';
        tituloNotificacion.textContent = 'Atención';
    }

    mensajeNotificacion.textContent = mensaje;
}


function cerrarModalNotificacion() {
    modalNotificacion.classList.add('hidden');

    if (window.pendingReload) {
        const tabActiva = localStorage.getItem('activeAdminTab') || 'dashboard';
        const url = new URL(window.location.origin + window.location.pathname);
        url.searchParams.set('tab', tabActiva);
        window.location.href = url.href;
    } else {
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('message');
        window.history.replaceState({}, '', url);
    }
}



// INICIALIZACIÓN AL CARGAR LA PÁGINA
document.addEventListener('DOMContentLoaded', () => {
    // 1. Sugerencias de tallas (datalist)
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

    // 2. Gestionar Tabs y Parámetros de URL
    const urlParams = new URLSearchParams(window.location.search);
    const estado = urlParams.get('status');
    const mensaje = urlParams.get('message');
    const urlTab = urlParams.get('tab');
    const storedTab = localStorage.getItem('activeAdminTab');

    const tabAMostrar = urlTab || storedTab || 'dashboard';
    cambiarPestaña(tabAMostrar, false);

    // 3. Mostrar notificación si hay status
    if (estado && mensaje) {
        mostrarNotificacion(estado, mensaje);
    }
});

