<?php
$titulo = "Inicio - Aetheria";
session_start();
$heroImage = "../img/home/Hero-Imagen.jpg";
include 'Cabecera.php';

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
                        <li><a href="#" class="sidebar-link text-black font-medium">Ver Todo</a></li>
                        <li><a href="#" class="sidebar-link">Abrigos & Chaquetas</a></li>
                        <li><a href="#" class="sidebar-link">Vestidos</a></li>
                        <li><a href="#" class="sidebar-link">Punto</a></li>
                        <li><a href="#" class="sidebar-link">Pantalones</a></li>
                        <li><a href="#" class="sidebar-link">Camisas & Tops</a></li>
                        <li class="pt-4"><a href="#" class="sidebar-link text-fashion-accent">Accesorios</a></li>
                        <li><a href="#" class="sidebar-link">Bolsos</a></li>
                        <li><a href="#" class="sidebar-link">Zapatos</a></li>
                    </ul>
                </div>

                <!-- FILTROS -->
                <div>
                    <h3 class="editorial-font text-2xl mb-6 italic">Filtros</h3>

                    <!-- Talla -->
                    <div class="mb-6">
                        <h4 class="uppercase text-xs tracking-widest font-semibold mb-3 text-gray-400">Talla</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                class="border border-gray-200 py-2 text-xs hover:border-black transition-colors">XS</button>
                            <button
                                class="border border-gray-200 py-2 text-xs hover:border-black transition-colors">S</button>
                            <button class="border border-black bg-black text-white py-2 text-xs">M</button>
                            <button
                                class="border border-gray-200 py-2 text-xs hover:border-black transition-colors">L</button>
                            <button
                                class="border border-gray-200 py-2 text-xs hover:border-black transition-colors">XL</button>
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="mb-6">
                        <h4 class="uppercase text-xs tracking-widest font-semibold mb-3 text-gray-400">Color</h4>
                        <div class="space-y-2">
                            <label
                                class="flex items-center text-sm font-light cursor-pointer hover:text-fashion-accent">
                                <input type="checkbox" class="custom-checkbox"> Negro
                            </label>
                            <label
                                class="flex items-center text-sm font-light cursor-pointer hover:text-fashion-accent">
                                <input type="checkbox" class="custom-checkbox"> Blanco
                            </label>
                            <label
                                class="flex items-center text-sm font-light cursor-pointer hover:text-fashion-accent">
                                <input type="checkbox" class="custom-checkbox"> Beige
                            </label>
                            <label
                                class="flex items-center text-sm font-light cursor-pointer hover:text-fashion-accent">
                                <input type="checkbox" class="custom-checkbox"> Dorado
                            </label>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div>
                        <h4 class="uppercase text-xs tracking-widest font-semibold mb-3 text-gray-400">Precio</h4>
                        <input type="range"
                            class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-fashion-black">
                        <div class="flex justify-between text-xs mt-2 text-gray-500">
                            <span>0€</span>
                            <span>1500€</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="w-full lg:w-4/5 2xl:w-5/6">

            <!-- Encabezado del Catálogo -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-10 pb-4 border-b border-gray-100">
                <div>
                    <span class="text-xs uppercase tracking-widest text-gray-400">Otoño / Invierno</span>
                    <h2 class="editorial-font text-4xl md:text-5xl mt-2">Ready to Wear</h2>
                </div>
                <div class="flex items-center gap-4 mt-4 md:mt-0">
                    <span class="text-sm text-gray-500">12 Resultados</span>
                    <select class="border-none text-sm bg-transparent font-medium focus:ring-0 cursor-pointer">
                        <option>Ordenar por</option>
                        <option>Precio: Bajo a Alto</option>
                        <option>Precio: Alto a Bajo</option>
                        <option>Más recientes</option>
                    </select>
                </div>
            </div>


            <!--CATÁLOGO -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-12">

                <!-- Product 1 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=1974&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Abrigo">

                        <!-- Badges -->
                        <div class="absolute top-0 left-0 p-3">
                            <span
                                class="bg-white/80 backdrop-blur text-[10px] uppercase tracking-widest px-2 py-1">Nuevo</span>
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
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Abrigo
                            Terciopelo</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Lana y Seda</p>
                        <p class="font-medium text-sm">890.00 €</p>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=2020&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Vestido">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Vestido
                            Seda Imperio</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Seda Orgánica</p>
                        <p class="font-medium text-sm">450.00 €</p>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?q=80&w=1974&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Blazer">
                        <div class="absolute top-0 left-0 p-3">
                            <span
                                class="bg-fashion-accent text-white text-[10px] uppercase tracking-widest px-2 py-1">Best
                                Seller</span>
                        </div>
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Blazer
                            Estructurado</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Lana Virgen</p>
                        <p class="font-medium text-sm">295.00 €</p>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1549662036-29a32c668631?q=80&w=1969&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Pantalón">
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
                            Pantalón Palazzo</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Lino Italiano</p>
                        <p class="font-medium text-sm">180.00 €</p>
                    </div>
                </div>

                <!-- Product 5 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1601924994987-69e26d50dc26?q=80&w=2070&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Top">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Top
                            Asimétrico</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Punto Fino</p>
                        <p class="font-medium text-sm">120.00 €</p>
                    </div>
                </div>

                <!-- Product 6 -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Bolso">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Bolso
                            Piel Italiana</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Piel Vacuna</p>
                        <p class="font-medium text-sm">1,200.00 €</p>
                    </div>
                </div>

                <!-- Product 7 (Nuevo para rellenar grid) -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1577907576223-9c869152b047?q=80&w=1969&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Suéter">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Suéter
                            Cashmere</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Cashmere 100%</p>
                        <p class="font-medium text-sm">380.00 €</p>
                    </div>
                </div>

                <!-- Product 8 (Nuevo para rellenar grid) -->
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden mb-4 bg-gray-50 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1594938298603-c8148c4dae35?q=80&w=2080&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                            alt="Traje">
                        <div
                            class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button
                                class="w-full bg-white/90 backdrop-blur hover:bg-fashion-black hover:text-white text-fashion-black text-xs uppercase tracking-widest py-3 transition-colors">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                    <div>
                        <h3 class="editorial-font text-xl group-hover:text-fashion-accent transition-colors">Traje
                            Sastre</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1 mb-2">Lana Fría</p>
                        <p class="font-medium text-sm">1,050.00 €</p>
                    </div>
                </div>

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
include 'Footer.html';
?>