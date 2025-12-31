<?php
$titulo = "Rebajas - Aetheria";
session_start();
// Imagen de fondo para el banner de rebajas - Opcional
$heroImage = "../img/home/Hero-Imagen.jpg";
include 'Cabecera.php';
require '../modelos/Productos/mostrar-productos.php';
require '../modelos/Productos/obtener-tallas-producto.php';
require '../modelos/categorias/mostrar-categoria.php';

$orden = $_GET['orden'] ?? '';
$filtroTallas = isset($_GET['tallas']) ? (is_array($_GET['tallas']) ? $_GET['tallas'] : [$_GET['tallas']]) : [];
$filtroCategoria = $_GET['categoria'] ?? '';
$filtroPrecio = $_GET['precio'] ?? 500;
$filtroBusqueda = $_GET['search'] ?? '';

$tallasSeleccionadas = $filtroTallas;

// Llamamos a mostrarProductos con el nuevo parámetro soloRebajas = true
$productos = mostrarProductos($orden, $filtroTallas, $filtroCategoria, $filtroPrecio, true, $filtroBusqueda, true);
$categorias = mostrarCategorias();
?>

<!-- BANNER REBAJAS -->
<section
    class="flex flex-col justify-center items-center h-[40vh] w-full overflow-hidden text-center text-white px-4 bg-fashion-black relative">
    <div class="absolute inset-0 opacity-40 bg-[url('../img/home/Hero-Imagen.jpg')] bg-cover bg-center grayscale"></div>
    <div class="relative z-10">
        <p class="uppercase tracking-[0.4em] text-[10px] mb-4 text-fashion-accent font-bold">Seasonal Sale</p>
        <h2 class="editorial-font text-6xl md:text-8xl mb-6 italic">Rebajas</h2>

    </div>
</section>

<!-- BLOQUE CENTRAL -->
<div class="w-full px-6 lg:px-12 py-16">

    <div class="flex flex-col lg:flex-row gap-12">

        <!-- ASIDE - BARRA LATERAL (Simplificada para Rebajas si se desea, o igual que index) -->
        <aside class="w-full lg:w-1/5 2xl:w-1/6 hidden lg:block">
            <div class="sticky top-32 space-y-12 pr-6 border-r border-gray-100 h-full">
                <!-- Filtros similares a index.php pueden ir aquí -->
                <div>
                    <h3 class="editorial-font text-2xl mb-6 italic">Filtros</h3>

                    <!-- Talla -->
                    <div class="mb-6">
                        <h4 class="uppercase text-xs tracking-widest font-semibold mb-3 text-gray-400">Talla</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <?php
                            $tallas = array_unique(mostrarTallas(), SORT_REGULAR);
                            foreach ($tallas as $talla) {
                                $tallaValor = $talla['talla'];
                                $isActive = in_array($tallaValor, $tallasSeleccionadas);
                                $classes = $isActive ? "bg-black text-white border-black" : "bg-white text-black border-gray-200 hover:border-black";
                                echo '<button type="button" onclick="toggleTalla(\'' . $tallaValor . '\', this)" class="talla-btn border py-2 text-xs transition-colors ' . $classes . '">' . $tallaValor . '</button>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="uppercase text-xs tracking-widest font-semibold text-gray-400">Precio Máx.</h4>
                            <span id="valor-precio-display"
                                class="text-xs font-bold text-fashion-black"><?= $filtroPrecio ?>€</span>
                        </div>
                        <input type="range" id="filter-price-slider" min="0" max="500" step="10"
                            value="<?= $filtroPrecio ?>" oninput="updatePriceDisplay(this.value)"
                            class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-fashion-black">
                    </div>

                    <button onclick="aplicarFiltros()"
                        class="w-full bg-fashion-black text-white py-4 text-xs hover:bg-fashion-accent mt-10 uppercase tracking-widest font-semibold">Aplicar</button>

                    <a href="rebajas-page.php"
                        class="block w-full text-center mt-4 text-[10px] uppercase tracking-widest text-gray-400 hover:text-black transition-colors">Limpiar
                        Filtros</a>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="w-full lg:w-4/5 2xl:w-5/6">

            <!-- Encabezado del Catálogo -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-4 border-b border-gray-100">
                <div>
                    <span class="text-xs uppercase tracking-widest text-red-600 font-bold">Ofertas Especiales</span>
                    <h2 class="editorial-font text-4xl md:text-5xl mt-2">Sale Selection</h2>
                </div>
                <div class="flex items-baseline gap-4 mt-4 md:mt-0">
                    <span class="text-sm text-gray-500"><?php echo count($productos); ?> Artículos en rebaja</span>
                </div>
            </div>

            <!--CATÁLOGO -->
            <?php if (count($productos) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">
                    <?php foreach ($productos as $producto) { ?>
                        <div class="group cursor-pointer">
                            <a href="ficha-producto.php?id=<?php echo $producto['id']; ?>" class="block">
                                <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                                    <img src="<?php echo '../' . $producto['imagen']; ?>"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

                                    <!-- Badges -->
                                    <div class="absolute top-0 left-0 p-3">
                                        <span
                                            class="bg-red-600 text-white text-[10px] font-bold px-3 py-1 text-center uppercase tracking-widest">-<?php echo intval($producto['descuento']); ?>%</span>
                                    </div>
                                </div>
                            </a>
                            <div>
                                <a href="ficha-producto.php?id=<?php echo $producto['id']; ?>">
                                    <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                    </h3>
                                </a>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </p>
                                <?php
                                $precioOriginal = $producto['precio'];
                                $descuento = $producto['descuento'];
                                $precioFinal = $precioOriginal - ($precioOriginal * ($descuento / 100));
                                ?>
                                <div class="flex items-center gap-3 mt-1">
                                    <span
                                        class="text-xs text-gray-400 line-through"><?php echo number_format($precioOriginal, 2); ?>€</span>
                                    <p class="font-medium text-sm text-red-600"><?php echo number_format($precioFinal, 2); ?>€
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php else: ?>
                <div class="py-20 text-center">
                    <i class="ph ph-tag-slash text-6xl text-gray-200 mb-6"></i>
                    <p class="text-gray-500 font-light">No hay productos en rebaja en este momento.</p>
                    <a href="index.php"
                        class="inline-block mt-8 text-xs uppercase tracking-widest font-bold border-b border-black pb-1 hover:text-fashion-accent hover:border-fashion-accent transition-colors">Volver
                        a la colección</a>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<script>
    window.tallasSeleccionadas = <?php echo json_encode($tallasSeleccionadas); ?>;

    function updatePriceDisplay(val) {
        document.getElementById('valor-precio-display').textContent = val + '€';
    }
</script>
<script src="../animaciones/filtrar-tallas.js"></script>

<?php include 'Footer.html'; ?>