<?php
$titulo = "Contacto - Aetheria";
session_start();
include 'Cabecera.php';
?>

<main class="w-full bg-white">
    <!-- TWO-COLUMN LAYOUT: IMAGE LEFT, CONTENT RIGHT -->
    <div class="flex flex-col lg:flex-row min-h-screen">

        <!-- LEFT: HERO IMAGE -->
        <div class="lg:w-1/2 lg:sticky lg:top-0 h-[50vh] lg:h-[calc(100vh-100px)]">
            <div class="relative w-full h-full overflow-hidden">
                <img src="../img/contacto-hero.jpg" alt="Aetheria Studio" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>
        </div>

        <!-- RIGHT: CONTENT (INFO + FORM) -->
        <div class="lg:w-1/2 flex flex-col">
            <div class="flex-1 px-10 lg:px-32 py-16 lg:py-24">

                <!-- HEADER -->
                <div class="mb-16 lg:mb-20">
                    <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400 mb-4">Contáctanos</p>
                    <h1 class="editorial-font text-4xl lg:text-6xl italic text-fashion-black">Aetheria Studio</h1>
                </div>

                <!-- CONTACT INFO -->
                <div class="space-y-12 mb-16">
                    <div>
                        <h3 class="text-[10px] uppercase tracking-[0.3em] font-bold text-fashion-black mb-4">Ubicación
                        </h3>
                        <p class="text-lg font-light text-gray-600 leading-relaxed">
                            Calle de la Moda, 42<br>
                            28001, Madrid, España
                        </p>
                    </div>

                    <div>
                        <h3 class="text-[10px] uppercase tracking-[0.3em] font-bold text-fashion-black mb-4">Consultas
                        </h3>
                        <ul class="space-y-3 text-lg font-light text-gray-600">
                            <li><a href="mailto:studio@aetheria.com"
                                    class="hover:text-fashion-accent transition-colors">studio@aetheria.com</a></li>
                            <li><a href="tel:+34912345678" class="hover:text-fashion-accent transition-colors">+34 912
                                    345 678</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-[10px] uppercase tracking-[0.3em] font-bold text-fashion-black mb-4">Social</h3>
                        <ul class="flex space-x-6 text-2xl text-gray-400">
                            <li><a href="#" class="hover:text-fashion-black transition-colors"><i
                                        class="ph ph-instagram-logo"></i></a></li>
                            <li><a href="#" class="hover:text-fashion-black transition-colors"><i
                                        class="ph ph-pinterest-logo"></i></a></li>
                            <li><a href="#" class="hover:text-fashion-black transition-colors"><i
                                        class="ph ph-facebook-logo"></i></a></li>
                        </ul>
                    </div>
                </div>

                <!-- CONTACT FORM -->
                <div class="border-t border-gray-200 pt-12">
                    <h3 class="text-[10px] uppercase tracking-[0.3em] font-bold text-fashion-black mb-8">Envíanos un
                        mensaje</h3>

                    <form id="contact-form" class="space-y-8">
                        <div class="relative">
                            <input type="text" id="name" name="name" required placeholder=" "
                                class="peer w-full bg-transparent border-0 border-b border-gray-200 py-4 focus:ring-0 focus:border-fashion-black transition-colors placeholder-transparent">
                            <label for="name"
                                class="absolute left-0 top-4 text-xs uppercase tracking-widest text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-focus:top-[-10px] peer-focus:text-xs">Nombre
                                Completo</label>
                        </div>

                        <div class="relative">
                            <input type="email" id="email" name="email" required placeholder=" "
                                class="peer w-full bg-transparent border-0 border-b border-gray-200 py-4 focus:ring-0 focus:border-fashion-black transition-colors placeholder-transparent">
                            <label for="email"
                                class="absolute left-0 top-4 text-xs uppercase tracking-widest text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-focus:top-[-10px] peer-focus:text-xs">Email</label>
                        </div>

                        <div class="relative">
                            <textarea id="message" name="message" rows="4" required placeholder=" "
                                class="peer w-full bg-transparent border-0 border-b border-gray-200 py-4 focus:ring-0 focus:border-fashion-black transition-colors placeholder-transparent resize-none"></textarea>
                            <label for="message"
                                class="absolute left-0 top-4 text-xs uppercase tracking-widest text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-focus:top-[-10px] peer-focus:text-xs">Mensaje</label>
                        </div>

                        <button type="submit"
                            class="w-full bg-fashion-black text-white py-5 text-[10px] uppercase tracking-[0.3em] font-bold rounded-lg hover:bg-fashion-accent transition-all duration-300 transform active:scale-95">
                            Enviar Mensaje
                        </button>

                        <div id="form-feedback" class="hidden text-center text-xs uppercase tracking-widest font-bold">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('contact-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const feedback = document.getElementById('form-feedback');
        const btn = this.querySelector('button[type="submit"]');
        const formData = new FormData(this);

        // Deshabilitar botón y mostrar estado de carga
        btn.disabled = true;
        btn.innerHTML = 'Enviando...';
        feedback.classList.add('hidden');

        try {
            const response = await fetch('../modelos/contacto/enviar-mensaje.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Éxito
                feedback.textContent = data.message;
                feedback.classList.remove('hidden', 'text-red-500');
                feedback.classList.add('text-green-600');
                this.reset();
            } else {
                // Error de validación
                feedback.textContent = data.message;
                feedback.classList.remove('hidden', 'text-green-600');
                feedback.classList.add('text-red-500');
            }
        } catch (error) {
            // Error de red o servidor
            feedback.textContent = 'Error al enviar el mensaje. Por favor, inténtalo de nuevo.';
            feedback.classList.remove('hidden', 'text-green-600');
            feedback.classList.add('text-red-500');
            console.error('Error:', error);
        } finally {
            // Restaurar botón
            btn.disabled = false;
            btn.innerHTML = 'Enviar Mensaje';
        }
    });
</script>

<?php include 'Footer.html'; ?>