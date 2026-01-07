<?php
session_start();
require_once "../config/conexion.php";
require_once "../modelos/productos/mostrar-productos.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$conn = conectar();

// Obtener detalles del producto (solo si está activo)
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: index.php");
    exit();
}

// Obtener tallas del producto
$stmtTallas = $conn->prepare("
    SELECT pt.id as variante_id, pt.stock, t.nombre as talla 
    FROM producto_tallas pt 
    JOIN tallas t ON pt.talla_id = t.id 
    WHERE pt.producto_id = ?
");
$stmtTallas->execute([$id]);
$tallas = $stmtTallas->fetchAll(PDO::FETCH_ASSOC);

$titulo = $producto['nombre'] . " - Aetheria";
include 'Cabecera.php';
?>

<main class="bg-white min-h-screen w-full lg:w-3/5 mx-auto" style="width: 60%;">
    <!-- Breadcrumbs -->
    <div class="w-full mx-auto px-6 py-6">
        <nav class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">
            <a href="index.php" class="hover:text-black transition-colors">Inicio</a>
            <span class="mx-2">/</span>
            <a href="index.php?categoria=<?php echo $producto['categoria_id']; ?>"
                class="hover:text-black transition-colors">Colección</a>
            <span class="mx-2">/</span>
            <span class="text-black"><?php echo htmlspecialchars($producto['nombre']); ?></span>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="w-full mx-auto px-6 mb-20">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

            <!-- Columna Izquierda: Imagen (50%) -->
            <div class="w-full lg:w-1/2 flex flex-col gap-4">
                <div class="bg-gray-100 aspect-[3/4] overflow-hidden relative">
                    <img src="<?php echo '../' . $producto['imagen']; ?>"
                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Columna Derecha: Info (Sticky) -->
            <div class="w-full lg:w-1/2 text-fashion-black">
                <div class="lg:sticky lg:top-24 space-y-8">

                    <!-- Cabecera -->
                    <div class="space-y-2">
                        <h1 class="font-editorial text-4xl uppercase tracking-wide">
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </h1>
                        <p class="text-xl font-medium"><?php echo number_format($producto['precio'], 2, ',', '.'); ?> €
                        </p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider pt-2">Ref.
                            <?php echo str_pad($producto['id'], 6, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>

                    <div class="h-px bg-gray-200"></div>

                    <!-- Descripción -->
                    <div class="text-sm text-gray-600 leading-7 font-light">
                        <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                    </div>

                    <!-- Botones / Selectores -->
                    <div class="space-y-6 pt-4">
                        <!-- Tallas -->
                        <div class="space-y-3">
                            <div class="flex justify-between text-[11px] uppercase tracking-widest font-bold">
                                <span>Talla</span>
                                <a href="#" class="underline text-gray-400 hover:text-black">Guía de tallas</a>
                            </div>
                            <div class="grid grid-cols-5 gap-2">
                                <?php if (!empty($tallas)): ?>
                                    <?php foreach ($tallas as $t): ?>
                                        <button type="button"
                                            class="size-btn py-2.5 border border-gray-300 text-xs hover:border-black transition-all"
                                            data-variante-id="<?php echo $t['variante_id']; ?>"
                                            data-talla="<?php echo htmlspecialchars($t['talla']); ?>">
                                            <?php echo htmlspecialchars($t['talla']); ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-xs text-gray-400 italic">Sin tallas disponibles</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Botón añadir -->
                        <button id="add-to-cart-btn"
                            class="w-full bg-black text-white py-4 text-xs uppercase tracking-[0.2em] font-bold hover:bg-gray-800 transition-colors">
                            Añadir a la Cesta
                        </button>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const sizeButtons = document.querySelectorAll('.size-btn');
                                const addToCartBtn = document.getElementById('add-to-cart-btn');
                                let selectedSize = null;

                                sizeButtons.forEach(btn => {
                                    btn.addEventListener('click', function () {
                                        // Resetear todos los botones
                                        sizeButtons.forEach(b => {
                                            b.classList.remove('border-black', 'bg-black', 'text-white');
                                            b.classList.add('border-gray-300');
                                        });

                                        // Marcar el seleccionado
                                        this.classList.remove('border-gray-300');
                                        this.classList.add('border-black', 'bg-black', 'text-white');
                                        selectedSize = this.dataset.talla;
                                    });
                                });

                                if (addToCartBtn) {
                                    addToCartBtn.addEventListener('click', async function () {
                                        if (!selectedSize) {
                                            alert('Por favor, selecciona una talla primero.');
                                            return;
                                        }

                                        const productId = <?= $id ?>;

                                        const variantId = this.dataset.varianteId;
                                        const tallaNombre = this.dataset.talla;

                                        try {
                                            const response = await fetch('../modelos/carrito/agregar-carrito.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: `producto_id=${productId}&variante_id=${variantId}&talla=${encodeURIComponent(tallaNombre)}&cantidad=1`
                                            });

                                            const result = await response.json();

                                            if (result.success) {
                                                // Actualizar contador en la cabecera
                                                const badge = document.getElementById('cart-count-badge');
                                                if (badge) {
                                                    badge.textContent = result.total_items;
                                                    badge.classList.remove('hidden');
                                                }

                                                // Abrir el sidebar del carrito automáticamente
                                                if (typeof closeSidebars === 'function') closeSidebars();
                                                const cartSidebar = document.getElementById('cart-sidebar');
                                                const sideOverlay = document.getElementById('side-overlay');
                                                if (cartSidebar && sideOverlay) {
                                                    cartSidebar.classList.add('login-sidebar-open');
                                                    cartSidebar.classList.remove('login-sidebar-close');
                                                    sideOverlay.classList.remove('hidden');
                                                    if (typeof loadCart === 'function') loadCart();
                                                }

                                                // Animación simple de éxito (opcional)
                                                this.textContent = '¡Añadido!';
                                                this.classList.remove('bg-black');
                                                this.classList.add('bg-fashion-accent');

                                                setTimeout(() => {
                                                    this.textContent = 'Añadir a la Cesta';
                                                    this.classList.remove('bg-fashion-accent');
                                                    this.classList.add('bg-black');
                                                }, 2000);

                                            } else {
                                                alert('Error: ' + result.message);
                                            }
                                        } catch (error) {
                                            console.error('Error al añadir al carrito:', error);
                                            alert('Ocurrió un error al añadir el producto a la cesta.');
                                        }
                                    });
                                }
                            });
                        </script>

                        <!-- Extra info -->
                        <div class="grid grid-cols-2 gap-4 text-center py-4 border-t border-b border-gray-100">
                            <div class="flex flex-col items-center gap-2">
                                <i class="ph ph-truck text-xl"></i>
                                <span class="text-[10px] uppercase font-bold tracking-widest">Envío Gratis</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 border-l border-gray-100">
                                <i class="ph ph-arrow-counter-clockwise text-xl"></i>
                                <span class="text-[10px] uppercase font-bold tracking-widest">Devoluciones</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div>
                        <details class="group py-4 border-b border-gray-100 cursor-pointer">
                            <summary
                                class="flex justify-between items-center text-[11px] uppercase font-bold tracking-widest list-none">
                                Composición
                                <span class="text-lg transition-transform group-open:rotate-45">+</span>
                            </summary>
                            <div class="pt-4 text-sm text-gray-500 font-light">
                                Fabricado con los mejores materiales seleccionados por Aetheria.
                            </div>
                        </details>
                        <details class="group py-4 border-b border-gray-100 cursor-pointer">
                            <summary
                                class="flex justify-between items-center text-[11px] uppercase font-bold tracking-widest list-none">
                                Envíos y Devoluciones
                                <span class="text-lg transition-transform group-open:rotate-45">+</span>
                            </summary>
                            <div class="pt-4 text-sm text-gray-500 font-light">
                                Plazo de entrega de 2 a 4 días laborables. Los gastos de envío son gratuitos en pedidos
                                superiores a 150€.
                            </div>
                        </details>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Productos Relacionados -->
    <section class="border-t border-gray-200 py-20">
        <div class="w-full mx-auto px-6">
            <h3 class="text-center font-editorial text-3xl uppercase tracking-wide mb-12">También te podría gustar</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 md:gap-x-8 gap-y-12">
                <?php
                // Obtener productos relacionados (misma categoría, activos, excluyendo el actual)
                $stmtRel = $conn->prepare("SELECT * FROM productos WHERE categoria_id = ? AND id != ? AND activo = 1 LIMIT 4");
                $stmtRel->execute([$producto['categoria_id'], $id]);
                $relacionados = $stmtRel->fetchAll(PDO::FETCH_ASSOC);

                foreach ($relacionados as $rel): ?>
                    <article class="group cursor-pointer">
                        <a href="ficha-producto.php?id=<?php echo $rel['id']; ?>" class="block">
                            <div class="overflow-hidden mb-4 relative">
                                <img src="<?php echo '../' . $rel['imagen']; ?>"
                                    class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105"
                                    alt="<?php echo htmlspecialchars($rel['nombre']); ?>">
                            </div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-sm font-medium uppercase tracking-wide">
                                        <?php echo htmlspecialchars($rel['nombre']); ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">Colección Permanente</p>
                                </div>
                                <span class="text-sm font-bold"><?php echo number_format($rel['precio'], 2, ',', '.'); ?>
                                    €</span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php include 'Footer.html'; ?>