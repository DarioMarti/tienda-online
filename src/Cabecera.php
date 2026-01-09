<?php
require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../config/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? "Aetheria" ?></title>

    <!-- ICONOS -->
    <script src="https://unpkg.com/phosphor-icons"></script>

    <!-- ESTILOS PERSONALIZADOS -->
    <link rel="stylesheet" href="../styles/input.css">

    <!-- TAILWIND CSS -->
    <link rel="stylesheet" href="../styles/output.css">

    <!-- FAVICON -->
    <link rel="icon" href="../img/home/Favicon_Aetherea.ico" type="image/x-icon">

    <!-- STRIPE -->
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body class="antialiased">
    <!-- BARRA SUPERIOR -->
    <div class="sticky top-0 w-full bg-fashion-black text-white text-[10px] py-2 text-center tracking-widest uppercase font-medium z-50 transition-transform duration-300"
        id="barra-superior">
        Envíos globales gratuitos en pedidos superiores a 300€
    </div>

    <!-- HEADER -->
    <header id="cabecera-principal" class="sticky top-0 w-full z-[60] py-6 px-6 lg:px-12 transition-all duration-300">
        <div class="w-full flex justify-between items-center">

            <!-- MENÚ DE LA IZQUIERDA -->
            <nav class="hidden lg:flex space-x-8 text-xs uppercase tracking-widest font-medium">
                <a href="index.php"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Inicio - Aetheria' ? 'text-fashion-accent' : '' ?>">
                    Home
                </a>
                <a href="rebajas-page.php"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Rebajas - Aetheria' ? 'text-red-600 font-bold' : 'text-red-500' ?>">
                    Rebajas
                </a>
                <a href="sobre-nosotros-page.php"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Sobre Nosotros - Aetheria' ? 'text-fashion-accent' : '' ?>">
                    Sobre Nosotros
                </a>
                <a href="contacto-page.php"
                    class="hover:text-fashion-accent transition-colors <?= ($titulo ?? '') === 'Contacto - Aetheria' ? 'text-fashion-accent' : '' ?>">
                    Contacto
                </a>
            </nav>

            <!-- MENÚ HAMBURGUESA MÓVIL -->
            <div class="lg:hidden text-2xl cursor-pointer" id="disparador-menu-movil">
                <i class="ph ph-list"></i>
            </div>

            <!-- LOGO -->
            <a href="index.php" class="absolute left-1/2 transform -translate-x-1/2">
                <img src="../img/Logotipo_Aetherea.svg" alt="Logo Aetheria" class="h-8">
            </a>

            <!-- ICONOS DE LA DERECHA -->
            <div class="flex items-center space-x-6 text-xl">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <!-- USUARIO REGISTRADO -->
                    <div class="relative group">
                        <span
                            class="text-xs uppercase tracking-widest hidden md:flex cursor-pointer font-medium mr-2 items-center gap-2">
                            <i class="ph ph-user-circle text-2xl"></i>
                            <span><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></span>
                        </span>
                        <!--SUBMENÚ DEL USUARIO REGISTRADO -->
                        <div
                            class="hidden group-hover:block absolute right-0  w-48 bg-white shadow-lg rounded-lg py-2 z-50">
                            <a href="perfil-page.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">Mi
                                Perfil</a>
                            <a href="mis-pedidos-page.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">Mis
                                Pedidos</a>

                            <?php if (esPersonalAutorizado()): ?>
                                <a href="admin-page.php"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-fashion-gray transition-colors">
                                    Panel de <?= $_SESSION['usuario']['rol'] === 'admin' ? 'administrador' : 'empleado' ?>
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
                        id="btn-login">
                        Login
                    </span>
                <?php endif; ?>

                <i class="ph ph-magnifying-glass cursor-pointer hover:scale-110 transition-transform search"
                    id="disparador-busqueda"></i>
                <div class="relative cursor-pointer" id="icono-carrito">
                    <i class="ph ph-handbag cesta hover:scale-110 transition-transform"></i>
                    <?php
                    $total_carrito = 0;
                    if (isset($_SESSION['usuario'])) {
                        $conn = conectar();
                        $stmt = $conn->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
                        $stmt->execute([$_SESSION['usuario']['id']]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $total_carrito = $result['total'] ?? 0;
                    } elseif (isset($_SESSION['carrito'])) {
                        foreach ($_SESSION['carrito'] as $item) {
                            $total_carrito += $item['cantidad'];
                        }
                    }
                    ?>
                    <span id="contador-carrito"
                        class="absolute bg-fashion-accent text-white font-bold flex items-center justify-center rounded-full z-20 <?= $total_carrito > 0 ? '' : 'hidden' ?>"
                        style="width: 13px; height: 13px; font-size: 8px; top: -3px; right: -3px; line-height: 1; padding: 0; margin: 0; pointer-events: none;">
                        <?= $total_carrito ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- BARRA DE BUSQUEDA FILTRADA -->
        <div id="contenedor-busqueda"
            class="hidden absolute left-0 top-full w-full bg-white border-b border-gray-100 shadow-sm py-2 px-6 lg:px-12 z-50 transform transition-all duration-300 origin-top">
            <div class="w-full">
                <input type="text" id="input-busqueda" placeholder="BUSCAR PRODUCTOS O CATEGORÍAS..."
                    class="w-full bg-transparent border-0 text-lg md:text-2xl editorial-font italic focus:ring-0 focus:outline-none py-4 placeholder:text-[8.5px] placeholder:uppercase placeholder:tracking-[0.2em] placeholder:font-sans placeholder:not-italic">
            </div>
        </div>
    </header>

    <!-- OVERLAY -->
    <div id="capa-superpuesta"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[45] hidden transition-opacity duration-300"></div>

    <!-- BARRA LATERAL DEL CARRITO -->
    <div id="sidebar-carrito" class="sidebar-lateral sidebar-cerrado z-[50]">
        <div class="flex justify-between items-center mb-10">
            <h2 class="editorial-font text-3xl italic">Tu Cesta</h2>
            <button id="cerrar-carrito" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- CONTENEDOR DE ITEMS DEL CARRITO -->
        <div id="contenedor-items-carrito" class="flex-1 overflow-y-auto space-y-6 mb-8 pr-2 custom-scrollbar">
            <p class="text-sm text-gray-500 text-center py-10">Cargando productos...</p>
        </div>

        <!-- FOOTER DEL CARRITO -->
        <div class="border-t border-gray-100 pt-8 mt-auto">
            <div class="flex justify-between items-center mb-6">
                <span class="text-xs uppercase tracking-[0.2em] font-bold text-gray-400">Subtotal</span>
                <span id="subtotal-carrito" class="text-lg font-bold">0,00 €</span>
            </div>
            <?php
            $cart_empty = !isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0;
            $checkout_url = isset($_SESSION['usuario']) ? 'checkout-page.php' : 'registro-usuario-page.php';
            ?>
            <a href="<?= $checkout_url ?>" id="btn-finalizar-compra"
                class="block w-full py-4 text-center text-xs uppercase tracking-[0.25em] font-semibold transition-colors rounded-lg <?= $cart_empty ? 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-fashion-black text-white hover:bg-fashion-accent' ?>">
                Finalizar Compra
            </a>
            <button id="continuar-comprando"
                class="w-full text-center mt-4 text-[10px] uppercase tracking-widest text-gray-400 hover:text-black transition-colors">
                Continuar Comprando
            </button>
        </div>
    </div>

    <!-- LOGIN BARRA LATERAL -->
    <div id="sidebar-login" class="sidebar-lateral sidebar-cerrado">

        <div class="flex justify-between items-center mb-10">
            <h2 class="editorial-font text-3xl italic">Iniciar Sesión</h2>
            <button id="cerrar-login" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- FORMULARIO LOGIN -->
        <form class="space-y-6 flex-1" action="../modelos/usuarios/sesion.php" method="POST">
            <div class="space-y-2">
                <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Email</label>
                <input type="email"
                    class="w-full  py-2 text-fashion-black focus:outline-none focus:border-fashion-black transition-colors bg-transparent"
                    id="campo-email" name="email" placeholder="tu@email.com">
            </div>

            <div class="space-y-2">
                <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Contraseña</label>
                <input type="password"
                    class="w-full py-2 text-fashion-black focus:outline-none focus:border-fashion-black transition-colors bg-transparent "
                    id="campo-pass" name="pass" placeholder="••••••••">
            </div>

            <div class="flex justify-between items-center text-xs text-gray-500 pt-2 formulario-checkbox"
                id="formulario-checkbox">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-300 text-fashion-black focus:ring-fashion-black ">
                    <span>Recordarme</span>
                </label>
                <a href="#" class="hover:text-fashion-black underline underline-offset-4">¿Olvidaste tu
                    contraseña?</a>
            </div>

            <button type="submit"
                class=" w-full bg-fashion-black text-white py-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-colors mt-8 rounded-lg">
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

    <!--MENÚ VERSIÓN MÓVIL -->
    <div id="sidebar-menu-movil" class="sidebar-lateral sidebar-cerrado z-[55] flex flex-col !max-w-none">
        <div class="flex justify-between items-center mb-10">
            <h2 class="editorial-font text-3xl italic">Menú</h2>
            <button id="cerrar-menu-movil" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <nav class="flex flex-col space-y-6 text-sm uppercase tracking-[0.2em] font-medium">
            <a href="index.php"
                class="hover:text-fashion-accent transition-colors py-2 border-b border-gray-50 <?= ($titulo ?? '') === 'Inicio - Aetheria' ? 'text-fashion-accent' : '' ?>">
                Home
            </a>
            <a href="rebajas-page.php"
                class="hover:text-fashion-accent transition-colors py-2 border-b border-gray-50 <?= ($titulo ?? '') === 'Rebajas - Aetheria' ? 'text-red-600 font-bold' : 'text-red-500' ?>">
                Rebajas
            </a>
            <a href="sobre-nosotros-page.php"
                class="hover:text-fashion-accent transition-colors py-2 border-b border-gray-50 <?= ($titulo ?? '') === 'Sobre Nosotros - Aetheria' ? 'text-fashion-accent' : '' ?>">
                Sobre Nosotros
            </a>
            <a href="contacto-page.php"
                class="hover:text-fashion-accent transition-colors py-2 border-b border-gray-50 <?= ($titulo ?? '') === 'Contacto - Aetheria' ? 'text-fashion-accent' : '' ?>">
                Contacto
            </a>
        </nav>

        <div class="mt-auto pt-10 border-t border-gray-100 flex flex-col space-y-4">
            <?php if (!isset($_SESSION['usuario'])): ?>
                <button id="btn-login-movil"
                    class="w-full py-4 border border-fashion-black text-xs uppercase tracking-widest font-semibold hover:bg-fashion-black hover:text-white transition-all">
                    Iniciar Sesión
                </button>
            <?php endif; ?>
        </div>
    </div>