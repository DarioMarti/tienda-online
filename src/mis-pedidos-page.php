<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
require_once "../modelos/pedidos/mostrar-pedidos.php";
$pedidos = mostrarPedidos($usuario['id']);

$titulo = "Mis Pedidos - Aetheria";
include 'Cabecera.php';
?>

<main class="min-h-screen bg-fashion-gray py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- Encabezado de la Página -->
        <div class="mb-12">
            <h1 class="font-editorial text-5xl italic text-fashion-black mb-4">Mis Historial de Pedidos</h1>
            <p class="text-gray-500 text-xs uppercase tracking-[0.3em]">Gestiona y revisa tus adquisiciones exclusivas
            </p>
        </div>

        <!-- Grid de Pedidos -->
        <div class="space-y-6">
            <?php if (empty($pedidos)): ?>
                <div class="bg-white rounded-lg shadow-xl p-20 text-center">
                    <div class="mb-8">
                        <i class="ph ph-shopping-bag text-8xl text-gray-200"></i>
                    </div>
                    <h2 class="font-editorial text-3xl italic text-fashion-black mb-4">Aún no has realizado pedidos</h2>
                    <p class="text-gray-500 text-sm uppercase tracking-widest mb-8">Te invitamos a explorar nuestra última
                        colección</p>
                    <a href="index.php"
                        class="inline-block bg-fashion-black text-white px-12 py-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-lg">
                        Ir a la Tienda
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <!-- Header del Pedido -->
                        <div
                            class="bg-gray-50 px-8 py-5 flex flex-wrap justify-between items-center border-b border-gray-100 gap-4">
                            <div class="flex items-center gap-8">
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-1">Pedido Realizado</p>
                                    <p class="text-sm font-semibold text-fashion-black">
                                        <?= date('d F, Y', strtotime($pedido['fecha'])) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-1">Total</p>
                                    <p class="text-sm font-bold text-fashion-black">
                                        <?= number_format($pedido['coste_total'], 2) ?>€
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-1">Enviar a</p>
                                    <p class="text-sm font-semibold text-fashion-black">
                                        <?= htmlspecialchars($pedido['nombre_destinatario']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-1">Pedido #<?= $pedido['id'] ?>
                                </p>
                            </div>
                        </div>

                        <!-- Cuerpo del Pedido -->
                        <div class="p-8">
                            <div class="flex flex-wrap items-center justify-between gap-8">
                                <div class="flex items-center gap-6">
                                    <?php
                                    // Determinar color de badge según estado
                                    $estadoClass = [
                                        'pendiente' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                        'pagado' => 'bg-green-50 text-green-700 border-green-100',
                                        'enviado' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'entregado' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'cancelado' => 'bg-red-50 text-red-700 border-red-100'
                                    ][$pedido['estado']] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                    ?>
                                    <div
                                        class="px-4 py-2 rounded-full border text-xs font-bold uppercase tracking-widest <?= $estadoClass ?>">
                                        <i class="ph ph-info mr-2"></i><?= ucfirst($pedido['estado']) ?>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        <?php if ($pedido['estado'] == 'entregado'): ?>
                                            El paquete fue entregado el
                                            <?= date('d/m/Y', strtotime($pedido['fecha'] . ' + 3 days')) ?>
                                        <?php elseif ($pedido['estado'] == 'enviado'): ?>
                                            Tu pedido está en camino
                                        <?php else: ?>
                                            Estamos procesando tu solicitud exclusiva
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <button onclick="verDetallesPedido(<?= $pedido['id'] ?>)"
                                        class="bg-white border border-gray-200 text-fashion-black px-8 py-3 text-[10px] uppercase tracking-widest font-bold hover:bg-fashion-gray transition-colors rounded-lg">
                                        Detalles del pedido
                                    </button>
                                </div>
                            </div>

                            <!-- Visualización de solo imágenes de productos -->
                            <div class="mt-8 pt-8 border-t border-gray-50 flex flex-wrap gap-3">
                                <?php foreach ($pedido['items'] as $item): ?>
                                    <div class="w-16 h-20 bg-fashion-gray rounded-md overflow-hidden flex-shrink-0 border border-gray-100 group transition-all hover:shadow-md"
                                        title="<?= htmlspecialchars($item['producto_nombre']) ?>">
                                        <img src="../<?= htmlspecialchars($item['producto_imagen']) ?>"
                                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                                            alt="<?= htmlspecialchars($item['producto_nombre']) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Sección de Ayuda -->
        <div
            class="mt-16 bg-white rounded-lg p-12 border border-gray-100 flex flex-wrap items-center justify-between gap-8">
            <div>
                <h3 class="font-editorial text-2xl italic text-fashion-black mb-2">¿Necesitas asistencia?</h3>
                <p class="text-gray-500 text-sm">Nuestro equipo de conserjería está disponible para cualquier duda sobre
                    tus pedidos.</p>
            </div>
            <div class="flex gap-8">
                <div class="flex flex-col">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Llámanos</p>
                    <p class="text-sm font-semibold text-fashion-black">+34 900 123 456</p>
                </div>
                <div class="flex flex-col">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Escríbenos</p>
                    <p class="text-sm font-semibold text-fashion-black">support@aetheria.com</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Detalles de Pedido (Copia simplificada del Admin) -->
<div id="modal-detalles-pedido"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center z-10">
            <div>
                <h2 class="font-editorial text-3xl italic text-fashion-black">Detalles del Pedido <span
                        id="det-id-pedido"></span></h2>
                <p class="text-gray-500 text-sm mt-1" id="det-fecha-pedido"></p>
            </div>
            <button onclick="cerrarModalDetallesPedido()"
                class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <div class="p-8">
            <!-- Información -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                    <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Estado del Pedido</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-gray-700">Estado Actual:</span>
                            <span id="det-badge-estado"
                                class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase"></span>
                        </div>
                        <p class="text-gray-500 text-xs">Si tienes alguna duda sobre el estado de tu pedido, contacta
                            con nuestro servicio de atención al cliente.</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                    <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Dirección de Entrega</h3>
                    <div class="space-y-2 text-sm">
                        <p id="det-nombre-receptor" class="font-bold text-fashion-black"></p>
                        <p id="det-direccion-envio" class="text-gray-700"></p>
                        <p id="det-ubicacion-envio" class="text-gray-700"></p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div>
                <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Artículos del Pedido</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500">Producto
                                </th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-center">
                                    Cant.</th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-right">
                                    Precio</th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-right">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody id="det-lista-items" class="divide-y divide-gray-100">
                            <!-- Items dinámicos -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold border-t-2 border-fashion-black">
                                <td colspan="3" class="px-6 py-4 text-right uppercase tracking-widest text-xs">Importe
                                    Total</td>
                                <td class="px-6 py-4 text-right text-xl text-fashion-black" id="det-total-pedido">0.00 €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function verDetallesPedido(id) {
        const modal = document.getElementById('modal-detalles-pedido');
        if (!modal) return;

        try {
            const respuesta = await fetch(`../modelos/pedidos/obtener-detalle-pedido.php?id=${id}`);
            const resultado = await respuesta.json();

            if (resultado.success) {
                const p = resultado.pedido;
                const items = resultado.items;

                document.getElementById('det-id-pedido').textContent = `#${p.id}`;
                document.getElementById('det-fecha-pedido').textContent = new Date(p.fecha).toLocaleDateString('es-ES', {
                    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                const badge = document.getElementById('det-badge-estado');
                badge.textContent = p.estado;
                badge.className = 'px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase ';
                const classes = {
                    'pendiente': 'bg-yellow-100 text-yellow-700',
                    'pagado': 'bg-green-100 text-green-700',
                    'enviado': 'bg-blue-100 text-blue-700',
                    'entregado': 'bg-purple-100 text-purple-700',
                    'cancelado': 'bg-red-100 text-red-700'
                };
                badge.className += (classes[p.estado] || 'bg-gray-100 text-gray-700');

                document.getElementById('det-nombre-receptor').textContent = p.nombre_destinatario;
                document.getElementById('det-direccion-envio').textContent = p.direccion_envio;
                document.getElementById('det-ubicacion-envio').textContent = `${p.ciudad}, ${p.provincia}`;

                const lista = document.getElementById('det-lista-items');
                lista.innerHTML = '';
                items.forEach(item => {
                    const fila = `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="../${item.producto_imagen}" class="w-10 h-10 object-cover rounded shadow-sm">
                                <span class="font-medium text-fashion-black">${item.producto_nombre}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold">${item.cantidad}</td>
                        <td class="px-6 py-4 text-right">${parseFloat(item.precio_unitario).toFixed(2)} €</td>
                        <td class="px-6 py-4 text-right font-bold text-fashion-black">${(item.cantidad * item.precio_unitario).toFixed(2)} €</td>
                    </tr>
                `;
                    lista.innerHTML += fila;
                });

                document.getElementById('det-total-pedido').textContent = `${parseFloat(p.coste_total).toFixed(2)} €`;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                alert(resultado.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('No se pudieron cargar los detalles del pedido.');
        }
    }

    function cerrarModalDetallesPedido() {
        const modal = document.getElementById('modal-detalles-pedido');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Cerrar al pulsar fuera del contenido
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('modal-detalles-pedido');
        if (e.target === modal) cerrarModalDetallesPedido();
    });
</script>

<?php include 'Footer.html'; ?>