<?php
$titulo = "Inicio - Aetheria";
session_start();
$heroImage = "../img/home/Hero-Imagen.jpg";
include 'Cabecera.php';
require '../modelos/Productos/mostrar-productos.php';
require '../modelos/Productos/obtener-tallas-producto.php';
require '../modelos/categorias/mostrar-categoria.php';

$orden = $_GET['orden'] ?? '';
$filtroTallas = isset($_GET['tallas']) && is_array($_GET['tallas']) ? $_GET['tallas'] : [];
$filtroCategoria = $_GET['categoria'] ?? '';
$filtroPrecio = $_GET['precio'] ?? 500; // Valor por defecto o del GET

$productos = mostrarProductos($orden, $filtroTallas, $filtroCategoria, $filtroPrecio);
$categorias = mostrarCategorias();


?>


<!-- HERO IMAGE -->
<section class="flex flex-col justify-center items-center h-[60vh] w-full overflow-hidden text-center text-white px-4"
    style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('<?= $heroImage ?>') center top / cover no-repeat;">

    <p class="uppercase tracking-[0.3em] text-xs mb-4">Fall Winter 2025</p>
    <h2 class="editorial-font text-5xl md:text-7xl mb-6 italic">
        La Colección
    </h2>

</section>


<!-- MARQUESINA -->
<div class="bg-white py-4 border-b border-gray-100 marquee-container w-full">
    <div class="marquee-content editorial-font text-xl md:text-2xl italic text-gray-300">
        New Arrivals — Sustainable Luxury — Fall Winter Collection — Timeless Elegance — New Arrivals — Sustainable
        Luxury — Fall Winter Collection — Timeless Elegance —
    </div>
</div>

<!-- BLOQUE CENTRAL -->
<div class="w-full px-6 lg:px-12 py-16">

    <div class="flex flex-col lg:flex-row gap-12">

        <!-- ASIDE - BARRA LATERAL -->
        <aside class="w-full lg:w-1/5 2xl:w-1/6 hidden lg:block">
            <div class="sticky top-32 space-y-12 pr-6 border-r border-gray-100 h-full">

                <!-- CATEGORÍAS -->
                <div>
                    <h3 class="editorial-font text-2xl mb-6 italic">Categorías</h3>
                    <ul class="space-y-4 text-sm tracking-wide font-light text-gray-600">
                        <li><a href="index.php"
                                class="sidebar-link text-black font-medium hover:text-fashion-accent transition-colors">Ver
                                Todo</a></li>
                        <?php
                        $hijosDe = [];
                        foreach ($categorias as $cat) {
                            $pid = (int) ($cat['categoria_padre_id'] ?? 0);
                            $hijosDe[$pid][] = $cat;
                        }

                        $idMap = [];
                        foreach ($categorias as $cat) {
                            $idMap[$cat['id']] = $cat;
                        }

                        $mostrados = [];

                        if (!function_exists('renderizarCategoria')) {
                            function renderizarCategoria($cat, $hijosDe, &$mostrados, $level = 1)
                            {
                                if (in_array($cat['id'], $mostrados))
                                    return;
                                $mostrados[] = $cat['id'];

                                $id = $cat['id'];
                                $nombre = htmlspecialchars($cat['nombre']);

                                // Estilos dinámicos según el nivel
                                $claseEstilo = ($level === 1) ? 'text-black font-medium' : 'text-xs text-gray-500';
                                $claseHover = 'hover:text-fashion-accent transition-colors';

                                echo '<li class="category-item' . ($level > 1 ? ' ml-2' : '') . '">';
                                echo '<a href="?categoria=' . $id . '" class="sidebar-link ' . $claseEstilo . ' ' . $claseHover . '">' . $nombre . '</a>';

                                if (isset($hijosDe[$id])) {
                                    $padding = ($level === 1) ? 'pl-4 mt-2 border-l border-gray-100 ml-1' : 'pl-4 mt-1';
                                    echo '<ul class="' . $padding . ' space-y-2">';
                                    foreach ($hijosDe[$id] as $hijo) {
                                        renderizarCategoria($hijo, $hijosDe, $mostrados, $level + 1);
                                    }
                                    echo '</ul>';
                                }
                                echo '</li>';
                            }
                        }

                        // 1. Mostrar categorías Raíz (sin padre o con padre que ya no existe)
                        foreach ($categorias as $cat) {
                            $pid = (int) ($cat['categoria_padre_id'] ?? 0);
                            if ($pid === 0 || !isset($idMap[$pid])) {
                                renderizarCategoria($cat, $hijosDe, $mostrados, 1);
                            }
                        }

                        // 2. Loop de seguridad: mostrar cualquier cosa que se haya perdido (p.ej. ciclos infinitos bloqueados)
                        foreach ($categorias as $cat) {
                            if (!in_array($cat['id'], $mostrados)) {
                                renderizarCategoria($cat, $hijosDe, $mostrados, 1);
                            }
                        }
                        ?>


                    </ul>
                </div>

                <!-- FILTROS -->
                <div>
                    <h3 class="editorial-font text-2xl mb-6 italic">Filtros</h3>

                    <!-- Talla -->
                    <div class="mb-6">
                        <h4 class="uppercase text-xs tracking-widest font-semibold mb-3 text-gray-400">Talla</h4>
                        <div class="grid grid-cols-3 gap-2">

                            <?php
                            $tallas = array_unique(mostrarTallas(), SORT_REGULAR);
                            $tallasSeleccionadas = $_GET['tallas'] ?? []; // Recuperar tallas seleccionadas de la URL
                            
                            foreach ($tallas as $talla) {
                                $tallaValor = $talla['talla'];
                                // Comprobar si esta talla está actualmente seleccionada
                                $isActive = in_array($tallaValor, $tallasSeleccionadas);
                                // Clases dinámicas
                                $classes = $isActive
                                    ? "bg-black text-white border-black"
                                    : "bg-white text-black border-gray-200 hover:border-black";

                                echo '<button 
                                    type="button"
                                    onclick="toggleTalla(\'' . $tallaValor . '\', this)"
                                    class="talla-btn border py-2 text-xs transition-colors ' . $classes . '">
                                    ' . $tallaValor . '
                                </button>';
                            }
                            ?>






                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="uppercase text-xs tracking-widest font-semibold text-gray-400">Precio</h4>
                            <span id="valor-precio-display"
                                class="text-xs font-bold text-fashion-black"><?= $filtroPrecio ?>€</span>
                        </div>
                        <input type="range" id="filter-price-slider" min="0" max="500" step="10"
                            value="<?= $filtroPrecio ?>" oninput="updatePriceDisplay(this.value)"
                            class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-fashion-black">
                        <div class="flex justify-between text-[10px] mt-2 text-gray-400 uppercase tracking-tighter">
                            <span>0€</span>
                            <span>500€</span>
                        </div>
                    </div>

                    <script>
                        function updatePriceDisplay(val) {
                            document.getElementById('valor-precio-display').textContent = val + '€';
                        }
                    </script>

                    <button onclick="aplicarFiltros()"
                        class="w-full bg-fashion-black text-white py-4 text-xs hover:bg-fashion-accent mt-10 uppercase tracking-widest font-semibold">Filtrar</button>
                </div>


            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="w-full lg:w-4/5 2xl:w-5/6">

            <!-- Encabezado del Catálogo -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-4 border-b border-gray-100">
                <div>
                    <span class="text-xs uppercase tracking-widest text-gray-400">Otoño / Invierno</span>
                    <h2 class="editorial-font text-4xl md:text-5xl mt-2">Ready to Wear</h2>
                </div>
                <div class="flex items-baseline gap-4 mt-4 md:mt-0">
                    <span class="text-sm text-gray-500"><?php echo count($productos); ?> Resultados</span>
                    <form method="GET">
                        <select name="orden" onchange="this.form.submit()"
                            class="border-none text-sm bg-transparent font-medium focus:ring-0 cursor-pointer">
                            <option value="">Ordenar por</option>
                            <option value="precio_asc">Precio: Bajo a Alto</option>
                            <option value="precio_desc">Precio: Alto a Bajo</option>
                            <option value="recientes">Más recientes</option>
                        </select>
                    </form>
                </div>
            </div>



            <!--CATÁLOGO -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">

                <!-- Product 1 -->


                <?php foreach ($productos as $producto) { ?>
                    <div class="group cursor-pointer">
                        <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                            <img src="<?php echo '../' . $producto['imagen']; ?>"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                alt="Abrigo">

                            <!-- Badges -->
                            <div class="absolute top-0 left-0 p-3">
                                <span
                                    class="bg-white/80 backdrop-blur text-[10px] uppercase tracking-widest px-2 py-1"><?php echo $producto['nombre']; ?></span>
                            </div>

                            <!-- Quick Add (Aparece en hover) -->
                            <div
                                class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <button
                                    class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                    Añadir al Carrito
                                </button>
                            </div>
                        </div>
                        <div>
                            <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">
                                <?php echo $producto['nombre']; ?>
                            </h3>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">
                                <?php echo $producto['descripcion']; ?>
                            </p>
                            <p class="font-medium text-sm"><?php echo $producto['precio']; ?></p>
                        </div>
                    </div>
                <?php } ?>


            </div>

            <!-- Paginación -->
            <div class="mt-20 flex justify-center">
                <div class="flex space-x-2">
                    <span
                        class="w-8 h-8 flex items-center justify-center border border-black bg-black text-white text-xs cursor-pointer">1</span>
                    <span
                        class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-black hover:text-black text-xs cursor-pointer transition-colors">2</span>
                    <span
                        class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-black hover:text-black text-xs cursor-pointer transition-colors">3</span>
                    <span
                        class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-black hover:text-black text-xs cursor-pointer transition-colors"><i
                            class="ph ph-arrow-right"></i></span>
                </div>
            </div>
        </main>
    </div>
</div>

<?php

// Cerrar bloque anterior si es necesario o simplemente insertar antes del include
?>
<script>
    // Inicializar array desde PHP
    window.tallasSeleccionadas = <?php echo json_encode($tallasSeleccionadas); ?>;
</script>
<script src="../animaciones/filtrar-tallas.js"></script>

<?php
include 'Footer.html';

?>