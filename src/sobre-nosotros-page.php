<?php
$titulo = "Sobre Nosotros - Aetheria";
session_start();
include 'Cabecera.php';
?>

<main class="w-full bg-white overflow-hidden">
    <!-- HERO SECTION -->
    <section class="relative w-full h-screen overflow-hidden">
        <img src="../img/sobre-nosotros-hero.jpg" alt="Aetheria Atelier"
            class="w-full h-full object-cover scale-105 animate-[ken-burns_20s_ease_infinite_alternative]">
        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-center px-6">
            <div class="space-y-4">
                <p class="text-[10px] uppercase tracking-[0.5em] text-white/70 animate-[fadeIn_1.5s_ease-out]">Est. 2020
                    — Madrid</p>
                <h1
                    class="editorial-font text-6xl md:text-9xl text-white italic leading-tight animate-[fadeIn_1.8s_ease-out]">
                    Aetheria
                </h1>
                <p
                    class="text-white/80 text-sm md:text-lg font-light tracking-[0.1em] max-w-xl mx-auto animate-[fadeIn_2s_ease-out]">
                    La intersección entre la elegancia radical y la artesanía atemporal.
                </p>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-12 flex flex-col items-center gap-4">
                <span class="text-[9px] uppercase tracking-[0.3em] text-white/50">Scroll</span>
                <div class="scroll-indicator-line"></div>
            </div>
        </div>
    </section>

    <!-- BRAND STORY - EDITORIAL GRID -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-32">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <!-- Left Side: Large Text -->
            <div class="col-span-12 lg:col-span-7 space-y-12">
                <h2 class="text-[10px] uppercase tracking-[0.4em] font-bold text-fashion-accent">El Manifiesto</h2>
                <p class="editorial-font text-4xl md:text-6xl italic text-fashion-black leading-[1.1]">
                    Creamos para aquellos que ven la moda como una forma de <span
                        class="text-fashion-accent">arquitectura personal</span>.
                </p>
                <div class="space-y-8 max-w-2xl">
                    <p class="text-lg text-gray-800 font-light leading-relaxed">
                        Fundada en 2020, Aetheria nace de una visión clara: crear moda que trascienda las tendencias
                        efímeras. No somos una marca de ropa; somos un estudio de diseño dedicado a la perfección de la
                        silueta y la calidad táctil.
                    </p>
                    <p class="text-lg text-gray-600 font-light leading-relaxed">
                        Cada pieza es el resultado de un diálogo íntimo entre la tradición artesanal y la innovación
                        contemporánea. Trabajamos con los mejores artesanos, seleccionamos materiales excepcionales y
                        dedicamos el tiempo necesario para que cada detalle sea un testimonio de excelencia.
                    </p>
                </div>
            </div>

            <!-- Right Side: Secondary Image & Floating Text -->
            <div class="col-span-12 lg:col-span-5 relative mt-12 lg:mt-0">
                <div class="relative w-full aspect-[3/4] overflow-hidden rounded-sm group shadow-2xl">
                    <img src="../img/home/Hero-Imagen.jpg" alt="Aetheria Detail"
                        class="w-full h-full object-cover grayscale transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-110">
                </div>
                <div
                    class="mt-8 lg:absolute lg:-bottom-6 lg:-left-6 glass-card p-10 max-w-xs shadow-2xl animate-[float_6s_ease-in-out_infinite]">
                    <p class="text-xs uppercase tracking-widest text-fashion-accent mb-4">Filosofía</p>
                    <p class="editorial-font italic text-xl">"La simplicidad es la máxima sofisticación."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- VALUES SECTION - INTERACTIVE CARDS -->
    <section class="bg-fashion-gray py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-24">
                <h2 class="text-[10px] uppercase tracking-[0.5em] font-bold text-fashion-black/40 mb-4">Nuestros Pilares
                </h2>
                <h3 class="editorial-font text-5xl italic">Valores que nos definen</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Artesanía -->
                <div
                    class="bg-white/80 backdrop-blur-md p-10 hover-lift group border border-fashion-black/5 rounded-sm transition-all duration-500">
                    <div
                        class="w-12 h-12 mb-8 flex items-center justify-center border border-fashion-black/10 rounded-full group-hover:bg-fashion-accent group-hover:text-white transition-colors duration-500">
                        <i class="ph ph-scissors text-2xl"></i>
                    </div>
                    <h4 class="text-[11px] uppercase tracking-[0.3em] font-bold mb-4">Artesanía</h4>
                    <p class="text-sm text-gray-500 font-light leading-relaxed">
                        Cada puntada es un compromiso. Trabajamos con manos expertas que heredan siglos de conocimiento
                        técnico.
                    </p>
                </div>

                <!-- Sostenibilidad -->
                <div
                    class="bg-white/80 backdrop-blur-md p-10 hover-lift group border border-fashion-black/5 rounded-sm transition-all duration-500 lg:mt-8">
                    <div
                        class="w-12 h-12 mb-8 flex items-center justify-center border border-fashion-black/10 rounded-full group-hover:bg-fashion-accent group-hover:text-white transition-colors duration-500">
                        <i class="ph ph-leaf text-2xl"></i>
                    </div>
                    <h4 class="text-[11px] uppercase tracking-[0.3em] font-bold mb-4">Sostenibilidad</h4>
                    <p class="text-sm text-gray-500 font-light leading-relaxed">
                        Moda consciente. Utilizamos fibras naturales y procesos de bajo impacto para proteger nuestro
                        legado compartido.
                    </p>
                </div>

                <!-- Atemporalidad -->
                <div
                    class="bg-white/80 backdrop-blur-md p-10 hover-lift group border border-fashion-black/5 rounded-sm transition-all duration-500">
                    <div
                        class="w-12 h-12 mb-8 flex items-center justify-center border border-fashion-black/10 rounded-full group-hover:bg-fashion-accent group-hover:text-white transition-colors duration-500">
                        <i class="ph ph-clock text-2xl"></i>
                    </div>
                    <h4 class="text-[11px] uppercase tracking-[0.3em] font-bold mb-4">Atemporalidad</h4>
                    <p class="text-sm text-gray-500 font-light leading-relaxed">
                        Diseñamos para el futuro. Nuestras piezas están concebidas para ignorar el calendario y durar
                        generaciones.
                    </p>
                </div>

                <!-- Innovación -->
                <div
                    class="bg-white/80 backdrop-blur-md p-10 hover-lift group border border-fashion-black/5 rounded-sm transition-all duration-500 lg:mt-8">
                    <div
                        class="w-12 h-12 mb-8 flex items-center justify-center border border-fashion-black/10 rounded-full group-hover:bg-fashion-accent group-hover:text-white transition-colors duration-500">
                        <i class="ph ph-lightbulb text-2xl"></i>
                    </div>
                    <h4 class="text-[11px] uppercase tracking-[0.3em] font-bold mb-4">Innovación</h4>
                    <p class="text-sm text-gray-500 font-light leading-relaxed">
                        Mirada vanguardista. Investigamos tejidos inteligentes y técnicas 3D para elevar la experiencia
                        del lujo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CRAFTSMANSHIP - OVERLAP SECTION -->
    <section class="py-32 overflow-hidden bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <!-- Text Column -->
                <div class="lg:col-span-5 space-y-10 order-2 lg:order-1">
                    <div class="space-y-4">
                        <h2 class="text-[10px] uppercase tracking-[0.4em] font-bold text-fashion-accent">Materiales</h2>
                        <h3 class="editorial-font text-5xl md:text-6xl italic leading-tight text-fashion-black">La
                            Calidad que se Siente
                        </h3>
                    </div>
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700 font-light leading-relaxed">
                            No comprometemos la excelencia. Viajamos por el mundo para encontrar las sedas italianas más
                            fluidas, los linos franceses más nobles y las lanas merino que parecen caricias.
                        </p>
                        <p class="text-lg text-gray-600 font-light leading-relaxed italic text-fashion-black">
                            "Para nosotros, el lujo es un sentimiento de comodidad absoluta y confianza inquebrantable."
                        </p>
                        <ul class="space-y-4 pt-4">
                            <li
                                class="flex items-center gap-4 text-xs uppercase tracking-widest font-semibold text-fashion-black">
                                <span class="w-8 h-px bg-fashion-accent"></span> Sedas 100% Orgánicas
                            </li>
                            <li
                                class="flex items-center gap-4 text-xs uppercase tracking-widest font-semibold text-fashion-black">
                                <span class="w-8 h-px bg-fashion-accent"></span> Producción Ética Local
                            </li>
                            <li
                                class="flex items-center gap-4 text-xs uppercase tracking-widest font-semibold text-fashion-black">
                                <span class="w-8 h-px bg-fashion-accent"></span> Trazabilidad Total
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Image Column -->
                <div class="lg:col-span-7 relative order-1 lg:order-2">
                    <div class="relative w-full h-[500px] lg:h-[600px] overflow-hidden rounded-sm shadow-xl">
                        <img src="../img/craftsmanship.jpg" alt="Craftsmanship Room" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-fashion-black/5 mix-blend-overlay"></div>
                    </div>
                    <!-- Decorative Elements simplified -->
                    <div
                        class="absolute -top-6 -right-6 w-24 h-24 border border-fashion-accent/20 hidden lg:block -z-10">
                    </div>
                    <div
                        class="absolute -bottom-6 -left-6 w-24 h-24 border border-fashion-accent/20 hidden lg:block -z-10">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VISION QUOTE -->
    <section class="stats-gradient text-white py-40">
        <div class="max-w-4xl mx-auto px-6 lg:px-12 text-center space-y-12">
            <i class="ph ph-quotes text-6xl text-fashion-accent opacity-50"></i>
            <h2 class="editorial-font text-4xl md:text-6xl italic leading-relaxed">
                "Nuestra visión es vestir el alma de quien busca la elegancia como su lenguaje más elocuente."
            </h2>
            <div class="flex items-center justify-center gap-6 pt-10">
                <a href="index.php"
                    class="bg-white text-fashion-black px-12 py-5 text-[10px] uppercase tracking-[0.3em] font-bold hover:bg-fashion-accent hover:text-white transition-all duration-500 rounded-sm">
                    Descubrir Aetheria
                </a>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 lg:gap-16">
                <div class="text-center group">
                    <div
                        class="editorial-font text-5xl md:text-6xl italic text-fashion-black mb-4 transition-transform duration-500 group-hover:scale-110">
                        2020</div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400">Nuestro Comienzo</p>
                </div>
                <div class="text-center group">
                    <div
                        class="editorial-font text-5xl md:text-6xl italic text-fashion-black mb-4 transition-transform duration-500 group-hover:scale-110">
                        12k+</div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400">Clientes Globales</p>
                </div>
                <div class="text-center group">
                    <div
                        class="editorial-font text-5xl md:text-6xl italic text-fashion-black mb-4 transition-transform duration-500 group-hover:scale-110">
                        50+</div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400">Artesanos de Élite</p>
                </div>
                <div class="text-center group">
                    <div
                        class="editorial-font text-5xl md:text-6xl italic text-fashion-black mb-4 transition-transform duration-500 group-hover:scale-110">
                        100%</div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400">Consciencia Ética</p>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-15px);
        }
    }

    @keyframes ken-burns {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.05);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<?php include 'Footer.html'; ?>