<!-- Iconos Phosphor -->
<script src="https://unpkg.com/phosphor-icons"></script>

<!-- Estilos Personalizados -->
<link rel="stylesheet" href="../styles/input.css">

<!-- Tailwind CSS -->
<link rel="stylesheet" href="../styles/output.css">

</head>

<body class="antialiased">
    <!-- BARRA SUPERIOR -->
    <div class="sticky top-0 w-full bg-fashion-black text-white text-[10px] py-2 text-center tracking-widest uppercase font-medium z-50 transition-transform duration-300"
        id="top-bar">
        Envíos globales gratuitos en pedidos superiores a 300€
    </div>

    <!-- HEADER -->
    <header id="main-header" class="sticky top-0 w-full z-40 py-6 px-6 lg:px-12 transition-all duration-300">
        <div class="w-full flex justify-between items-center">

            <!-- Menú Izquierda -->
            <nav class="hidden lg:flex space-x-8 text-xs uppercase tracking-widest font-medium">
                <a href="index.php"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Inicio - Aetheria' ? 'text-fashion-accent' : '' ?>">
                    Home
                </a>
                <a href="#"
                    class="hover:text-fashion-accent transition-colors font-bold <?= ($titulo ?? '') === 'Inicio - Sobre Nosotros' ? 'text-fashion-accent' : '' ?>">
                    Sobre Nosotros
                </a>
                <a href="#"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Inicio - Contacto' ? 'text-fashion-accent' : '' ?>">
                    Contacto
                </a>
            </nav>

            <!-- Mobile Menu Icon -->
            <div class="lg:hidden text-2xl cursor-pointer">
                <i class="ph ph-list"></i>
            </div>

            <!-- Logo Central -->
            <a href="index.php" class="absolute left-1/2 transform -translate-x-1/2">
                <img src="../img/Logotipo_Aetherea.svg" alt="Logo Aetheria" class="h-8">
            </a>

            <!-- Iconos Derecha -->
            <div class="flex items-center space-x-6 text-xl">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <!-- USUARIO REGISTRADO -->
                    <div class="relative group">
                        <span
                            class="text-xs uppercase tracking-widest hidden md:flex cursor-pointer font-medium mr-2 items-center gap-2">
                            <i class="ph ph-user-circle text-2xl"></i>
                            <span><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></span>
                        </span>
                        <!-- Dropdown menu (opcional) -->
                        <div
                            class="hidden group-hover:block absolute right-0  w-48 bg-white shadow-lg rounded-lg py-2 z-50">
                            <a href="perfil-page.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">Mi
                                Perfil</a>
                            <a href="mis-pedidos-page.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">Mis
                                Pedidos</a>

                            <?php if (htmlspecialchars($_SESSION['usuario']['rol']) == "admin"): ?>
                                <a href="admin-page.php"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">
                                    Panel de administrador
                                </a>
                            <?php endif; ?>
                            <hr class="my-2">

                            <a href="../modelos/usuarios/cerrar-sesion.php"
                                class="block px-4 py-2 text-sm text-red-600 hover:bg-fashion-gray transition-colors">Cerrar
                                Sesión</a>
                        </div>
                    </div>
                <?php else: ?>

                    <!--USUARIO NO REGISTRADO-->
                    <span class="text-xs uppercase tracking-widest hidden md:block cursor-pointer font-medium mr-2 login"
                        id="login">Login</span>
                <?php endif; ?>

                <i class="ph ph-magnifying-glass cursor-pointer hover:scale-110 transition-transform search"
                    id="search"></i>
                <div class="relative cursor-pointer hover:scale-110 transition-transform">
                    <i class="ph ph-handbag cesta"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-fashion-accent rounded-full"></span>
                </div>
            </div>
        </div>
    </header>

    <!-- LOGIN SIDEBAR -->
    <div id="login-sidebar" class="login-sidebar login-sidebar-close">


        <!-- Cabecera del Sidebar -->
        <div class="flex justify-between items-center mb-10">
            <h2 class="editorial-font text-3xl italic">Iniciar Sesión</h2>
            <button id="close-login" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- Formulario -->
        <form class="space-y-6 flex-1" action="../modelos/usuarios/sesion.php" method="POST">
            <div class="space-y-2">
                <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Email</label>
                <input type="email"
                    class="w-full  py-2 text-fashion-black focus:outline-none focus:border-fashion-black transition-colors bg-transparent"
                    id="formName" name="email" placeholder="tu@email.com">
            </div>

            <div class="space-y-2">
                <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Contraseña</label>
                <input type="password"
                    class="w-full py-2 text-fashion-black focus:outline-none focus:border-fashion-black transition-colors bg-transparent "
                    id="formPassword" name="pass" placeholder="••••••••">
            </div>

            <div class="flex justify-between items-center text-xs text-gray-500 pt-2 checkboxForm" id="checkboxForm">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-300 text-fashion-black focus:ring-fashion-black ">
                    <span>Recordarme</span>
                </label>
                <a href="#" class="hover:text-fashion-black underline underline-offset-4">¿Olvidaste tu
                    contraseña?</a>
            </div>

            <button type="submit"
                class=" w-full bg-fashion-black text-white py-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-colors mt-8 rounded rounded-lg">
                Entrar
            </button>
        </form>

        <!-- Footer del Sidebar -->
        <div class="border-t border-gray-100 pt-8 text-center">
            <p class="text-sm text-gray-500 mb-4">¿Aún no tienes cuenta?</p>
            <a href="registro-usuario-page.php"
                class="inline-block border border-fashion-black text-fashion-black px-8 py-3 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-black hover:text-white transition-all duration-300">
                Crear Cuenta
            </a>
        </div>

    </div>