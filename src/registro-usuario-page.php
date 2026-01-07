<?php
session_start();
$titulo = "Registro - Aetheria";
include 'Cabecera.php';
?>

<!-- FORMULARIO DE REGISTRO -->
<main class="min-h-screen bg-fashion-gray flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-lg shadow-xl">

        <div class="text-center">
            <h2 class="font-editorial text-4xl italic text-fashion-black mb-2">Crear Cuenta</h2>
            <p class="text-sm text-gray-500 tracking-wide">Únete a la familia Aetheria</p>
        </div>

        <!-- FORMULARIO REGISTRO -->
        <form class="mt-8 space-y-6" action="../modelos/usuarios/agregar-usuario.php" method="POST">

            <!-- NOMBRE DE USUARIO -->
            <div class="space-y-2">
                <label for="username" class="text-xs uppercase tracking-widest font-semibold text-gray-700">
                    Nombre de Usuario
                </label>
                <input id="username" name="nombre" type="text" required
                    class="w-full px-4 py-3 bg-[#F5F5F5] focus:border-fashion-black focus:outline-none transition-colors bg-transparent text-fashion-black placeholder-gray-400"
                    placeholder="Tu nombre de usuario">
            </div>

            <!-- EMAIL -->
            <div class="space-y-2">
                <label for="email" class="text-xs uppercase tracking-widest font-semibold text-gray-700">
                    Email
                </label>
                <input id="email" name="email" type="email" required
                    class="w-full px-4 py-3 bg-[#F5F5F5] focus:border-fashion-black focus:outline-none transition-colors bg-transparent text-fashion-black placeholder-gray-400"
                    placeholder="tu@email.com">
            </div>

            <!-- CONTRASEÑA -->
            <div class="space-y-2">
                <label for="password" class="text-xs uppercase tracking-widest font-semibold text-gray-700">
                    Contraseña
                </label>
                <input id="password" name="contraseña" type="password" required
                    class="w-full px-4 py-3 bg-[#F5F5F5] focus:border-fashion-black focus:outline-none transition-colors bg-transparent text-fashion-black placeholder-gray-400"
                    placeholder="••••••••">
            </div>

            <!-- TÉRMINOS Y CONDICIONES -->
            <div class="flex items-start space-x-3 pt-2">
                <input id="terms" name="terms" type="checkbox" required
                    class="mt-1 rounded border-gray-300 text-fashion-black focus:ring-fashion-black focus:ring-2">
                <label for="terms" class="text-xs text-gray-600 leading-relaxed">
                    Acepto los <a href="#"
                        class="text-fashion-black underline underline-offset-2 hover:text-fashion-accent transition-colors">términos
                        y condiciones</a> y la <a href="#"
                        class="text-fashion-black underline underline-offset-2 hover:text-fashion-accent transition-colors">política
                        de privacidad</a>
                </label>
            </div>

            <!-- BOTÓN DE REGISTRO -->
            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Crear Cuenta
                </button>
            </div>

            <!-- LINK A LOGIN -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes cuenta?
                    <a href="index.php"
                        class="text-fashion-black font-semibold hover:text-fashion-accent transition-colors underline underline-offset-2">
                        Inicia Sesión
                    </a>
                </p>
            </div>

        </form>

    </div>
</main>

<?php
include 'Footer.html';
?>