<?php
$titulo = "Vestido Seda Aetheria - Aetheria";
include 'Cabecera.php';
?>

<main class="bg-white min-h-screen w-full lg:w-3/5 mx-auto" style="width: 60%;">
    <!-- Breadcrumbs -->
    <div class="w-full mx-auto px-6 py-6">
        <nav class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">
            <a href="index.php" class="hover:text-black transition-colors">Inicio</a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-black transition-colors">Colección</a>
            <span class="mx-2">/</span>
            <span class="text-black">Tenuzela Azules</span>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="w-full mx-auto px-6 mb-20">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

            <!-- Columna Izquierda: Imagen (50%) -->
            <div class="w-full lg:w-1/2 flex flex-col gap-4">
                <div class="bg-gray-100 aspect-[3/4] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=1000&auto=format&fit=crop"
                        alt="Vestido Principal" class="w-full h-full object-cover">
                </div>
                <!-- Grilla de imagenes secundaria si se desea, o solo una grande como en la referencia simple -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-100 aspect-[3/4] overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=1000&auto=format&fit=crop"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="bg-gray-100 aspect-[3/4] overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=1000&auto=format&fit=crop"
                            class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Info (Sticky) -->
            <div class="w-full lg:w-1/2 text-fashion-black">
                <div class="lg:sticky lg:top-24 space-y-8">

                    <!-- Cabecera -->
                    <div class="space-y-2">
                        <h1 class="font-editorial text-4xl uppercase tracking-wide">Tenzuela Azules</h1>
                        <p class="text-xl font-medium">129,00 €</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider pt-2">Ref. 29751-02</p>
                    </div>

                    <div class="h-px bg-gray-200"></div>

                    <!-- Descripción -->
                    <div class="text-sm text-gray-600 leading-7 font-light">
                        <p>Sandalias de tacón de ante de color azul marino con brillos. Un modelo elegante diseñado para
                            ser el protagonista de los mejores looks de eventos. Cuenta con pulsera al tobillo que
                            permite un ajuste óptimo.</p>
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
                                <button
                                    class="py-2.5 border border-gray-300 text-xs hover:border-black transition-all">36</button>
                                <button
                                    class="py-2.5 border border-gray-300 text-xs hover:border-black transition-all">37</button>
                                <button class="py-2.5 border border-black bg-black text-white text-xs">38</button>
                                <button
                                    class="py-2.5 border border-gray-300 text-xs hover:border-black transition-all">39</button>
                                <button
                                    class="py-2.5 border border-gray-300 text-xs text-gray-300 cursor-not-allowed">40</button>
                            </div>
                        </div>

                        <!-- Botón añadir -->
                        <button
                            class="w-full bg-black text-white py-4 text-xs uppercase tracking-[0.2em] font-bold hover:bg-gray-800 transition-colors">
                            Añadir a la Cesta
                        </button>

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
                                Interior: Piel<br>
                                Exterior: Textil<br>
                                Suela: Goma
                            </div>
                        </details>
                        <details class="group py-4 border-b border-gray-100 cursor-pointer">
                            <summary
                                class="flex justify-between items-center text-[11px] uppercase font-bold tracking-widest list-none">
                                Envíos y Devoluciones
                                <span class="text-lg transition-transform group-open:rotate-45">+</span>
                            </summary>
                            <div class="pt-4 text-sm text-gray-500 font-light">
                                Plazo de entrega de 2 a 4 días laborables.
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
                <!-- Item 1 -->
                <article class="group cursor-pointer">
                    <div class="overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=1000&auto=format&fit=crop"
                            class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium uppercase tracking-wide">Modelo Alfa</h4>
                            <p class="text-xs text-gray-500 mt-1">Azul Marino</p>
                        </div>
                        <span class="text-sm font-bold">145 €</span>
                    </div>
                </article>

                <!-- Item 2 -->
                <article class="group cursor-pointer">
                    <div class="overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1611312449408-fcece27cdbb7?q=80&w=1000&auto=format&fit=crop"
                            class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium uppercase tracking-wide">Bolso Beta</h4>
                            <p class="text-xs text-gray-500 mt-1">Negro Mate</p>
                        </div>
                        <span class="text-sm font-bold">89 €</span>
                    </div>
                </article>

                <!-- Item 3 -->
                <article class="group cursor-pointer">
                    <div class="overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1550614000-4b9519e09eb3?q=80&w=1000&auto=format&fit=crop"
                            class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium uppercase tracking-wide">Abrigo Gamma</h4>
                            <p class="text-xs text-gray-500 mt-1">Lana Merino</p>
                        </div>
                        <span class="text-sm font-bold">299 €</span>
                    </div>
                </article>

                <!-- Item 4 -->
                <article class="group cursor-pointer">
                    <div class="overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1579613832125-5d34a13ffe2a?q=80&w=1000&auto=format&fit=crop"
                            class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium uppercase tracking-wide">Collar Delta</h4>
                            <p class="text-xs text-gray-500 mt-1">Oro 18k</p>
                        </div>
                        <span class="text-sm font-bold">120 €</span>
                    </div>
                </article>
            </div>
        </div>
    </section>

</main>

<?php include 'Footer.html'; ?>