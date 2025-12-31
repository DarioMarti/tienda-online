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
                                    <button
                                        class="bg-fashion-black text-white px-8 py-3 text-[10px] uppercase tracking-widest font-bold hover:bg-fashion-accent transition-colors rounded-lg">
                                        Seguimiento
                                    </button>
                                    <button
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

<?php include 'Footer.html'; ?>