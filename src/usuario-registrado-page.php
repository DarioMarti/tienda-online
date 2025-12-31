<?php
session_start();
$titulo = "Bienvenido - Aetheria";
include 'Cabecera.php';
?>

<!-- Página de Confirmación -->
<div class="flex items-center justify-center bg-white px-6 py-20" style="min-height: 60vh;">

    <div class="max-w-4xl w-full text-center">

        <!-- Animación de Entrada -->
        <div class="fade-in-up">


            <!-- Título Principal -->
            <h1
                class="editorial-font text-6xl md:text-7xl lg:text-6xl italic text-fashion-black mb-6 tracking-tight leading-none ">
                Gracias
            </h1>

            <!-- Mensajes -->
            <div class="space-y-4 mb-12 max-w-xl mx-auto">
                <p class="text-lg md:text-xl text-gray-700 font-light leading-relaxed">
                    Tu cuenta ha sido creada exitosamente
                </p>
                <p class="text-base text-gray-500 font-light">
                    Bienvenido a la familia Aetheria
                </p>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="index.php" class="bton mt-10">
                    <button class="bton">Explorar Tienda</button>
                </a>

                <a href="#"
                    class="px-10 py-4 border-2 border-gray-300 text-gray-700 text-xs uppercase tracking-[0.25em] font-semibold hover:border-fashion-black hover:text-fashion-black transition-all duration-300">
                    Mi Cuenta
                </a>
            </div>

            <!-- Información Adicional -->
            <div class="mt-16 pt-8 border-t border-gray-100">
                <p class="text-xs text-gray-400 tracking-wider">
                    ¿Necesitas ayuda?
                    <a href="#" class="text-fashion-accent hover:underline underline-offset-4 ml-1 font-medium">Contacta
                        con nosotros</a>
                </p>
            </div>

        </div>

    </div>

</div>

<?php
include 'Footer.html';
?>