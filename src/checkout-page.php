<?php
session_start();
require_once "../config/conexion.php";

// LLEVA A LA HOME SI EL CARRITO ESTA VACIO
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit();
}

$carrito = $_SESSION['carrito'];
$items = [];
$subtotal = 0;
$envio = 0.00;

$conn = conectar();
foreach ($carrito as $item) {
    $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
    $stmt->execute([$item['producto_id']]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        $total_item = $producto['precio'] * $item['cantidad'];
        $subtotal += $total_item;
        $items[] = [
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'imagen' => $producto['imagen'],
            'talla' => $item['talla'],
            'cantidad' => $item['cantidad']
        ];
    }
}

$total = $subtotal + $envio;

$titulo = "Finalizar Pedido - Aetheria";
include 'Cabecera.php';
?>

<main class="min-h-screen bg-fashion-gray py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12">
            <h1 class="font-editorial text-5xl italic text-fashion-black mb-4">Finalizar Compra</h1>
            <p class="text-gray-500 text-xs uppercase tracking-[0.3em]">Casi has completado tu adquisición exclusiva</p>
        </div>


        <form id="checkout-form" action="#" method="POST" class="checkout-grid items-start">
            <!-- BLOQUE IZQUIERDA - DATOS DEL CLIENTE -->
            <div class="space-y-12 w-full">
                <!-- 1. DATOS DE ENVIO -->
                <section class="space-y-6">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-bold border-b border-gray-200 pb-4">1. Datos de
                        Envío</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                                Nombre Completo</label>
                            <input type="text" name="nombre_destinatario" required
                                class="w-full px-4 py-3 bg-white border border-gray-100 focus:border-fashion-black outline-none transition-colors text-sm"
                                value="<?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']['nombre']) : '' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Email</label>
                            <input type="email" name="email_contacto" required readonly
                                class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed text-gray-50 outline-none transition-colors text-sm"
                                value="<?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']['email']) : '' ?>">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label
                                class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Dirección</label>
                            <input type="text" name="direccion" required placeholder="Calle, número, piso..."
                                class="w-full px-4 py-3 bg-white border border-gray-100 focus:border-fashion-black outline-none transition-colors text-sm ">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                                Código Postal</label>
                            <input type="text" name="cp" required
                                class="w-full px-4 py-3 bg-white border border-gray-100 focus:border-fashion-black outline-none transition-colors text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Ciudad</label>
                            <input type="text" name="ciudad" required
                                class="w-full px-4 py-3 bg-white border border-gray-100 focus:border-fashion-black outline-none transition-colors text-sm">
                        </div>
                    </div>
                </section>

                <!-- MÉTODO DE PAGO -->
                <section class="space-y-4">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-bold border-b border-gray-200 pb-4">
                        2. Método de Pago
                    </h2>
                    <div id="payment-element" class="bg-white p-6 rounded-lg border border-gray-100">
                        <!-- Stripe insertará aquí el formulario de pago -->
                    </div>
                    <div id="payment-message" class="hidden text-red-500 text-xs text-center mt-2"></div>
                </section>
            </div>

            <!-- BLOQUE DERECHO - RESUMEN DEL PEDIDO -->
            <div class="lg:sticky lg:top-32 w-full mt-10 lg:mt-0">
                <div class="bg-white p-10 rounded-lg border border-gray-100 shadow-sm flex flex-col h-full">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-bold border-b border-gray-50 pb-4 mb-8">
                        Resumen de Pedido
                    </h2>

                    <!-- LISTA DE PRODUCTOS -->
                    <div class="flex-1 space-y-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar mb-10">
                        <?php foreach ($items as $item): ?>
                            <div class="flex gap-4">
                                <div class="w-16 h-20 bg-fashion-gray overflow-hidden rounded-md flex-shrink-0">
                                    <img src="../<?= $item['imagen'] ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold uppercase tracking-widest truncate"><?= $item['nombre'] ?>
                                    </h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">Talla: <?= $item['talla'] ?> | Cant:
                                        <?= $item['cantidad'] ?>
                                    </p>
                                    <p class="text-xs font-semibold mt-2"><?= number_format($item['precio'], 2, ',', '.') ?>
                                        €</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- PRECIO -->
                    <div class="space-y-6 pt-6 border-t border-gray-50 mt-auto">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span
                                    class="text-gray-400 uppercase tracking-widest text-[10px] font-bold">Subtotal</span>
                                <span class="font-medium"><?= number_format($subtotal, 2, ',', '.') ?> €</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400 uppercase tracking-widest text-[10px] font-bold">
                                    Gastos de Envío</span>
                                <span class="font-medium"><?= number_format($envio, 2, ',', '.') ?> €</span>
                            </div>
                            <hr class="border-fashion-gray my-4">
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-xs uppercase tracking-[0.2em] font-black">Total a Pagar</span>
                                <span
                                    class="text-2xl font-editorial italic font-bold"><?= number_format($total, 2, ',', '.') ?>€</span>
                            </div>
                        </div>

                        <button type="submit" id="realizar-pago-btn"
                            class="w-full bg-fashion-black text-white py-5 text-sm uppercase tracking-[0.3em] font-bold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-xl transform hover:-translate-y-1 mt-4 flex items-center justify-center">
                            <span id="button-text">Pagar Ahora con Stripe</span>
                            <div id="spinner" class="hidden ph ph-circle-notch animate-spin ml-2 text-lg"></div>
                        </button>

                    </div>
                </div>
            </div>
        </form>
    </div>
</main>


<script src="../animaciones/stripe-handler.js"></script>
<?php include 'Footer.html'; ?>