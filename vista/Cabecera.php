<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if (isset($titulo)) {
        echo $titulo;
    } else {
        echo "Fuse";
    } ?></title>

    <!-- Fuentes Google: Bodoni Moda (Editorial) y Jost (Geométrica moderna) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,600..96,900;1,6..96,400&family=Jost:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Iconos Phosphor -->
    <script src="https://unpkg.com/phosphor-icons"></script>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="../styles/output.css">

</head>

<body class="antialiased">
<!-- ANUNCIO SUPERIOR -->
<div class="bg-fashion-black text-white text-[10px] py-2 text-center tracking-widest uppercase font-medium w-full">
    Envíos globales gratuitos en pedidos superiores a 300€
</div>

<!-- HEADER (Full Width) -->
<header id="main-header" class="fixed w-full top-[31px] z-50 py-6 px-6 lg:px-12 transition-all duration-300">
    <div class="w-full flex justify-between items-center">

        <!-- Menú Izquierda -->
        <nav class="hidden lg:flex space-x-8 text-xs uppercase tracking-widest font-medium">
            <a href="#" class="hover:text-fashion-accent transition-colors">Novedades</a>
            <a href="#" class="hover:text-fashion-accent transition-colors font-bold text-fashion-accent">Shop</a>
            <a href="#" class="hover:text-fashion-accent transition-colors">Journal</a>
        </nav>

        <!-- Mobile Menu Icon -->
        <div class="lg:hidden text-2xl cursor-pointer">
            <i class="ph ph-list"></i>
        </div>

        <!-- Logo Central -->
        <a href="#" class="absolute left-1/2 transform -translate-x-1/2">
            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight">AETHERIA</h1>
        </a>

        <!-- Iconos Derecha -->
        <div class="flex items-center space-x-6 text-xl">
            <span class="text-xs uppercase tracking-widest hidden md:block cursor-pointer font-medium mr-2">Login</span>
            <i class="ph ph-magnifying-glass cursor-pointer hover:scale-110 transition-transform"></i>
            <div class="relative cursor-pointer hover:scale-110 transition-transform">
                <i class="ph ph-handbag"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-fashion-accent rounded-full"></span>
            </div>
        </div>
    </div>
</header>