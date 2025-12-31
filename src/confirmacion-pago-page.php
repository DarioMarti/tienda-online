<?php
session_start();
$titulo = "¡Gracias por tu compra! - Aetheria";
include 'Cabecera.php';
?>

<main class="min-h-[80vh] bg-white flex items-center justify-center py-20 px-4">
    <div class="max-w-3xl w-full text-center space-y-12">

        <!-- Icono de Éxito Animado (Simulado con CSS) -->
        <div class="relative w-32 h-32 mx-auto">
            <div class="absolute inset-0 bg-green-50 rounded-full animate-ping opacity-20"></div>
            <div
                class="relative bg-white border border-green-100 w-32 h-32 rounded-full flex items-center justify-center shadow-sm">
                <i class="ph ph-check-circle text-6xl text-green-500"></i>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="font-editorial text-6xl italic text-fashion-black">Pedido Confirmado</h1>
            <p class="text-xl text-gray-600 font-light max-w-xl mx-auto leading-relaxed">
                <?= isset($_SESSION['mensaje_exito']) ? htmlspecialchars($_SESSION['mensaje_exito']) : 'Gracias por confiar en <span class="font-editorial italic">Aetheria</span>. Hemos recibido tu pedido y te hemos enviado un email con los detalles.' ?>
            </p>
        </div>

        <div class="bg-fashion-gray p-8 rounded-lg max-w-md mx-auto space-y-4">
            <div class="flex justify-between text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                <span>Estado del Pedido</span>
                <span class="text-fashion-black">Pagado</span>
            </div>
            <div class="flex justify-between text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                <span>Número de Referencia</span>
                <span
                    class="text-fashion-black">#<?= isset($_SESSION['ultimo_pedido_id']) ? $_SESSION['ultimo_pedido_id'] : strtoupper(substr(uniqid(), -8)) ?></span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-6 justify-center pt-8">
            <a href="index.php"
                class="bg-fashion-black text-white px-12 py-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-lg">
                Volver a la Tienda
            </a>
            <a href="mis-pedidos-page.php"
                class="bg-white border border-gray-200 text-fashion-black px-12 py-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-gray transition-all duration-300 rounded-lg">
                Ver mis pedidos
            </a>
        </div>

        <p class="text-[10px] text-gray-400 uppercase tracking-widest pt-12">
            Si tienes alguna duda, escríbenos a <span
                class="text-fashion-black font-semibold">concierge@aetheria.com</span>
        </p>
    </div>
</main>

<?php include 'Footer.html'; ?>