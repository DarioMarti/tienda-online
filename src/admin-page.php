<?php
require_once '../config/conexion.php';
require("../modelos/usuarios/mostrar-usuarios.php");
require("../modelos/categorias/mostrar-categoria.php");
require("../modelos/productos/mostrar-productos.php");
require("../modelos/pedidos/mostrar-pedidos.php");
require("../modelos/informes/obtener-informes.php");

// Verificación de seguridad
restringirAccesoPagina();

// Obtener usuarios para la gestión (todos, incluidos inactivos)
$usuarios = mostrarUsuarios(false);
$categorias = mostrarCategorias(false); // mostrarCategorias no filtra por defecto si no le pasamos false/true? Revisar implementación. 
// Aparentemente mostrarCategorias recibe soloActivos. Entonces false es correcto para "NO solo activos" -> "Todos".
$productos = mostrarProductos('', [], '', null, false); // El último parámetro es "soloActivos", false = mostrar todos.
$pedidos = mostrarPedidos(null, true);
$totalVentas = 0;

$ingresosMensuales = obtenerIngresosMensuales();
$productosMasVendidos = obtenerProductosMasVendidos();

foreach ($pedidos as $pedido) {
    if ($pedido['estado'] !== 'cancelado') {
        $totalVentas += $pedido['coste_total'];
    }
}
?>
<?php
include 'Cabecera.php';
?>

<script>
    // Pasar ID del administrador actual a JS para protecciones de edición
    const idUsuarioActual = <?= $_SESSION['usuario']['id'] ?>;
    const todosLosProductos = <?php echo json_encode($productos); ?>;
</script>

<div class="min-h-screen bg-fashion-gray flex">

    <!-- Sidebar de Navegación -->
    <aside class="w-96 bg-white shadow-xl hidden md:block sticky top-24 h-[calc(100vh-6rem)] z-10 overflow-y-auto">
        <div class="p-6">
            <h2 class="font-editorial text-2xl italic text-fashion-black mb-8">
                Panel <?= $_SESSION['usuario']['rol'] === 'admin' ? 'Admin' : 'Empleado' ?>
            </h2>
            <nav class="space-y-2">
                <button onclick="cambiarPestaña('dashboard')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-fashion-black bg-fashion-gray transition-colors"
                    data-tab="dashboard">
                    <i class="ph ph-squares-four mr-2"></i>Dashboard
                </button>
                <button onclick="cambiarPestaña('pedidos')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="pedidos">
                    <i class="ph ph-package mr-2"></i>Pedidos
                </button>
                <button onclick="cambiarPestaña('productos')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="productos">
                    <i class="ph ph-t-shirt mr-2"></i>Productos
                </button>
                <button onclick="cambiarPestaña('categorias')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="categorias">
                    <i class="ph ph-tag mr-2"></i>Categorías
                </button>
                <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                    <button onclick="cambiarPestaña('usuarios')"
                        class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                        data-tab="usuarios">
                        <i class="ph ph-users mr-2"></i>Usuarios
                    </button>
                    <button onclick="cambiarPestaña('informes')"
                        class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                        data-tab="informes">
                        <i class="ph ph-chart-line-up mr-2"></i>Informes
                    </button>
                <?php endif; ?>
            </nav>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="flex-1 p-8 pt-24">

        <!-- Sección Dashboard (Resumen) -->
        <section id="seccion-dashboard" class="tab-content block">
            <h1 class="font-editorial text-4xl italic text-fashion-black mb-8">Resumen General</h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Cards de Resumen -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Total Ventas</p>
                            <h3 class="text-2xl font-bold text-fashion-black"><?= $totalVentas ?>€</h3>
                        </div>
                        <div class="p-2 bg-green-100 rounded-full text-green-600">
                            <i class="ph ph-currency-eur text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Pedidos</p>
                            <h3 class="text-2xl font-bold text-fashion-black"><?= count($pedidos) ?></h3>
                        </div>
                        <div class="p-2 bg-blue-100 rounded-full text-blue-600">
                            <i class="ph ph-shopping-bag text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Usuarios</p>
                            <h3 class="text-2xl font-bold text-fashion-black"><?= count($usuarios) ?></h3>
                        </div>
                        <div class="p-2 bg-purple-100 rounded-full text-purple-600">
                            <i class="ph ph-users text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Productos</p>
                            <h3 class="text-2xl font-bold text-fashion-black"><?= count($productos) ?></h3>
                        </div>
                        <div class="p-2 bg-orange-100 rounded-full text-orange-600">
                            <i class="ph ph-tag text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="seccion-pedidos" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Pedidos</h1>
                <button onclick="abrirModalPedido()"
                    class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                    <i class="ph ph-plus mr-2"></i>Nuevo Pedido
                </button>
            </div>
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">ID
                                </th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Cliente</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Fecha</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Total</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Estado</th>
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($pedidos)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No hay pedidos
                                        registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr
                                        class="hover:bg-gray-50 transition-colors <?php echo ($pedido['activo'] == 0) ? 'bg-red-50 opacity-75' : ''; ?>">
                                        <td class="px-6 py-4 font-bold text-fashion-black">
                                            #<?= $pedido['id'] ?>
                                            <?php if ($pedido['activo'] == 0): ?>
                                                <span
                                                    class="block text-[9px] text-red-600 uppercase tracking-widest mt-1">Eliminado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-fashion-black">
                                                <?= htmlspecialchars($pedido['nombre_destinatario']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= htmlspecialchars($pedido['usuario_email'] ?? 'Venta Anónima') ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-fashion-black">
                                            <?= number_format($pedido['coste_total'], 2) ?> €
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $estadoClass = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                                'pagado' => 'bg-green-100 text-green-700',
                                                'enviado' => 'bg-blue-100 text-blue-700',
                                                'entregado' => 'bg-purple-100 text-purple-700',
                                                'cancelado' => 'bg-red-100 text-red-700'
                                            ][$pedido['estado']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider <?= $estadoClass ?>">
                                                <?= ucfirst($pedido['estado']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button onclick="verDetallesPedido(<?= $pedido['id'] ?>)"
                                                class="text-gray-400 hover:text-fashion-accent transition-colors"
                                                title="Ver Detalles">
                                                <i class="ph ph-eye text-xl"></i>
                                            </button>
                                            <button onclick='editarPedido(<?= json_encode($pedido) ?>)'
                                                class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                                <i class="ph ph-pencil-simple text-xl"></i>
                                            </button>
                                            <?php if ($pedido['activo'] == 1): ?>
                                                <button onclick="eliminarPedido(<?= $pedido['id'] ?>)"
                                                    class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                                    <i class="ph ph-trash text-xl"></i>
                                                </button>
                                            <?php else: ?>
                                                <button onclick="activarPedido(<?= $pedido['id'] ?>)"
                                                    class="text-gray-400 hover:text-green-500 transition-colors" title="Reactivar">
                                                    <i class="ph ph-arrow-u-up-left text-xl"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>


        <section id="seccion-productos" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Productos</h1>
                <button onclick="abrirModalProducto()"
                    class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                    <i class="ph ph-plus mr-2"></i>Nuevo Producto
                </button>
            </div>
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 w-20">
                                    Imagen</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Producto</th>
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 w-1/3">
                                    Descripción</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Precio</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Stock</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Categoría</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Estado</th>
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                    <tr
                                        class="hover:bg-gray-50 transition-colors <?php echo ($producto['activo'] == 0) ? 'bg-red-50 opacity-75' : ''; ?>">
                                        <td class="px-6 py-4">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden relative">
                                                <?php if ($producto['activo'] == 0): ?>
                                                    <!-- Icono eliminado retirado según solicitud -->
                                                <?php endif; ?>
                                                <?php if (!empty($producto['imagen'])): ?>
                                                    <img src="../<?= htmlspecialchars($producto['imagen']) ?>"
                                                        alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                        class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="ph ph-image text-2xl"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-fashion-black">
                                                <?= htmlspecialchars($producto['nombre']) ?>
                                                <?php if ($producto['activo'] == 0): ?>
                                                    <span
                                                        class="block text-[9px] text-red-600 uppercase tracking-widest mt-1">Eliminado</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!empty($producto['descripcion'])): ?>
                                                <div class="text-xs text-gray-500 line-clamp-3">
                                                    <?= htmlspecialchars($producto['descripcion']) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">Sin descripción</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-fashion-black">
                                                <?= number_format($producto['precio'], 2) ?> €
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider 
                                                <?= $producto['stock'] > 10 ? 'bg-green-100 text-green-700' : ($producto['stock'] > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                                <?= $producto['stock'] ?> uds
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php
                                            // Buscar nombre de categoría
                                            $categoriaNombre = 'Sin categoría';
                                            foreach ($categorias as $cat) {
                                                if ($cat['id'] == $producto['categoria_id']) {
                                                    $categoriaNombre = $cat['nombre'];
                                                    break;
                                                }
                                            }
                                            echo htmlspecialchars($categoriaNombre);
                                            ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider 
                                            <?= $producto['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button onclick='editarProducto(<?= json_encode($producto) ?>)'
                                                class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                                <i class="ph ph-pencil-simple text-xl"></i>
                                            </button>
                                            <?php if ($producto['activo']): ?>
                                                <button
                                                    onclick="eliminarProducto(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')"
                                                    class="text-gray-400 hover:text-red-500 transition-colors" title="Desactivar">
                                                    <i class="ph ph-trash text-xl"></i>
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    onclick="activarProducto(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')"
                                                    class="text-gray-400 hover:text-green-500 transition-colors" title="Activar">
                                                    <i class="ph ph-arrow-u-up-left text-xl"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="seccion-categorias" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Categorías</h1>
                <button onclick="abrirModalCategoria()"
                    class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                    <i class="ph ph-plus mr-2"></i>Nueva Categoría
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Nombre
                                </th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Descripción
                                </th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Categoría Padre
                                </th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($categorias as $categoria): ?>
                                <tr
                                    class="hover:bg-gray-50 transition-colors <?php echo ($categoria['activo'] == 0) ? 'bg-red-50 opacity-75' : ''; ?>">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-fashion-black">
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                            <?php if ($categoria['activo'] == 0): ?>
                                                <span
                                                    class="block text-[9px] text-red-600 uppercase tracking-widest mt-1">Eliminado</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($categoria['descripcion']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-600">
                                            <?= htmlspecialchars($categoria['categoria_padre_id'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider 
                                        <?= $categoria['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= $categoria['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button onclick='editarCategoria(<?= json_encode($categoria) ?>)'
                                            class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                            <i class="ph ph-pencil-simple text-xl"></i>
                                        </button>
                                        <?php if ($categoria['activo']): ?>
                                            <button
                                                onclick="eliminarCategoria(<?= $categoria['id'] ?>, '<?= htmlspecialchars($categoria['nombre']) ?>')"
                                                class="text-gray-400 hover:text-red-500 transition-colors" title="Desactivar">
                                                <i class="ph ph-trash text-xl"></i>
                                            </button>
                                        <?php else: ?>
                                            <button
                                                onclick="activarCategoria(<?= $categoria['id'] ?>, '<?= htmlspecialchars($categoria['nombre']) ?>')"
                                                class="text-gray-400 hover:text-green-500 transition-colors" title="Activar">
                                                <i class="ph ph-arrow-u-up-left text-xl"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
            <section id="seccion-usuarios" class="tab-content hidden">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Usuarios</h1>
                    <button onclick="abrirModalUsuario()"
                        class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                        <i class="ph ph-plus mr-2"></i>Nuevo Usuario
                    </button>
                </div>
                <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                        Usuario</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">Rol
                                    </th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                        Estado</th>
                                    <th
                                        class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr
                                        class="hover:bg-gray-50 transition-colors <?php echo ($usuario['activo'] == 0) ? 'bg-red-50 opacity-75' : ''; ?>">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-fashion-black">
                                                <?= htmlspecialchars($usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? '')) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($usuario['email']) ?>
                                            </div>
                                            <?php if ($usuario['activo'] == 0): ?>
                                                <span
                                                    class="block text-[9px] text-red-600 uppercase tracking-widest mt-1">Eliminado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-800">
                                                <?= htmlspecialchars($usuario['rol']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider <?= $usuario['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button onclick='editarUsuario(<?= json_encode($usuario) ?>)'
                                                class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                                <i class="ph ph-pencil-simple text-xl"></i>
                                            </button>
                                            <?php if ($usuario['activo']): ?>
                                                <button
                                                    onclick="eliminarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')"
                                                    class="text-gray-400 hover:text-red-500 transition-colors" title="Desactivar">
                                                    <i class="ph ph-trash text-xl"></i>
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    onclick="activarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')"
                                                    class="text-gray-400 hover:text-green-500 transition-colors" title="Activar">
                                                    <i class="ph ph-arrow-u-up-left text-xl"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section id="seccion-informes" class="tab-content hidden">
                <h1 class="font-editorial text-4xl italic text-fashion-black mb-8">Informes</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Gráfico de ventas (Placeholder) -->
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold mb-4">Ingresos Mensuales</h3>
                        <div class="h-64 bg-gray-50 flex items-center justify-center text-gray-400">
                            Gráfico de Ingresos
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold mb-4">Productos Más Vendidos</h3>
                        <ul class="space-y-3">
                            <?php foreach ($productosMasVendidos as $prod): ?>
                                <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span><?= htmlspecialchars($prod['nombre']) ?></span>
                                    <span class="font-bold"><?= $prod['total_vendido'] ?> uds</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    </main>
</div>

<!-- Modal Usuario (Crear/Editar) -->
<div id="modal-usuario" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 id="titulo-modal" class="font-editorial text-3xl italic text-fashion-black">Nuevo Usuario</h2>
            <button onclick="cerrarModalUsuario()" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="formulario-usuario" action="../modelos/usuarios/admin-agregar-usuario.php" method="POST" class="p-8">
            <input type="hidden" name="user_id" id="id-usuario">
            <input type="hidden" name="action" id="accion-formulario" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2" id="grupo-password">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Contraseña</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                    <p class="text-xs text-gray-500 italic mt-1" id="pista-password">Dejar en blanco para mantener la
                        actual al editar.</p>
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Rol</label>
                    <select name="rol" id="rol"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black bg-white">
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Estado</label>
                    <select name="activo" id="activo"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black bg-white">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="cerrarModalUsuario()"
                    class="flex-1 bg-gray-200 text-gray-700 py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all rounded-lg shadow-lg">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Categoría (Crear/Editar) -->
<div id="modal-categoria" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 id="titulo-modal-categoria" class="font-editorial text-3xl italic text-fashion-black">Nueva Categoría
            </h2>
            <button onclick="cerrarModalCategoria()" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="formulario-categoria" action="../modelos/categorias/crear-categoria.php" method="POST" class="p-8">
            <input type="hidden" name="category_id" id="id-categoria">
            <input type="hidden" name="action" id="accion-formulario-categoria" value="create">

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="nombre-categoria" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Descripción</label>
                    <textarea name="descripcion" id="descripcion-categoria" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black"></textarea>
                </div>


                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Categoría Padre</label>
                    <select name="categoria_padre_id" id="id-padre-categoria"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black bg-white">
                        <option value="">Ninguna (Categoría Principal)</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                        <?php endforeach; ?>
                        <!-- Las categorías se cargarían dinámicamente aquí -->
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="cerrarModalCategoria()"
                    class="flex-1 bg-gray-200 text-gray-700 py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all rounded-lg shadow-lg">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pedido (Crear/Editar) -->
<div id="modal-pedido" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 id="titulo-modal-pedido" class="font-editorial text-3xl italic text-fashion-black">Nuevo Pedido</h2>
            <button onclick="cerrarModalPedido()" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="formulario-pedido" action="../modelos/pedidos/crear-pedido.php" method="POST" class="p-8">
            <input type="hidden" name="pedido_id" id="id-pedido">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Email Usuario</label>
                    <input type="email" name="usuario_email" id="email-usuario-pedido" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black"
                        placeholder="Email del cliente">
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Coste Total (€)</label>
                    <input type="number" step="0.01" name="coste_total" id="coste-total-pedido" required readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-fashion-black font-bold">
                </div>

                <!-- Sección de Productos Dinámicos -->
                <div class="md:col-span-2 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xs uppercase tracking-widest font-bold text-gray-500">Artículos del Pedido</h4>
                        <button type="button" onclick="añadirFilaProducto()"
                            class="text-xs bg-fashion-black text-white px-4 py-2 rounded hover:bg-fashion-accent transition-colors flex items-center gap-2">
                            <i class="ph ph-plus"></i> Añadir Producto
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-lg">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                                        Producto</th>
                                    <th
                                        class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500 w-24">
                                        Cant.</th>
                                    <th
                                        class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500 text-right w-24">
                                        Precio</th>
                                    <th
                                        class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500 text-right w-24">
                                        Subtotal</th>
                                    <th
                                        class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500 text-center w-12">
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="constructor-items-pedido" class="divide-y divide-gray-100">
                                <!-- Filas de productos dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre
                        Destinatario</label>
                    <input type="text" name="nombre_destinatario" id="nombre-destinatario-pedido" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Dirección Envío</label>
                    <textarea name="direccion_envio" id="direccion-envio-pedido" required rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad-pedido" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Provincia</label>
                    <input type="text" name="provincia" id="provincia-pedido" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Estado</label>
                    <select name="estado" id="estado-pedido" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black bg-white">
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
            </div>

            <!-- Aviso Global de Stock -->
            <div id="aviso-stock-pedido" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex gap-3">
                    <i class="ph ph-warning-circle text-red-500 text-xl"></i>
                    <div>
                        <p class="text-sm font-bold text-red-700">Stock Insuficiente</p>
                        <ul class="text-xs text-red-600 list-disc ml-4 mt-1" id="lista-avisos-stock">
                            <!-- Errores dinámicos -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8">

                <button type="button" onclick="cerrarModalPedido()"
                    class="flex-1 bg-gray-200 text-gray-700 py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all rounded-lg shadow-lg">
                    Guardar Pedido
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Detalles de Pedido -->
<div id="modal-detalles-pedido"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center z-10">
            <div>
                <h2 class="font-editorial text-3xl italic text-fashion-black">Detalles del Pedido <span
                        id="det-id-pedido"></span></h2>
                <p class="text-gray-500 text-sm mt-1" id="det-fecha-pedido"></p>
            </div>
            <button onclick="cerrarModalDetallesPedido()"
                class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <div class="p-8">
            <!-- Información del Cliente -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                    <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Información del Cliente
                    </h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-semibold text-gray-700">Nombre:</span> <span
                                id="det-nombre-cliente"></span></p>
                        <p><span class="font-semibold text-gray-700">Email:</span> <span id="det-email-cliente"></span>
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-700">Estado:</span>
                            <span id="det-badge-estado"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                    <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Dirección de Envío</h3>
                    <div class="space-y-2 text-sm">
                        <p id="det-direccion-envio" class="text-gray-700"></p>
                        <p id="det-ubicacion-envio" class="text-gray-700"></p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div>
                <h3 class="text-xs uppercase tracking-widest font-bold text-gray-500 mb-4">Artículos del Pedido</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500">Producto
                                </th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-center">
                                    Cantidad</th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-right">
                                    Precio Unit.</th>
                                <th
                                    class="px-6 py-3 text-xs uppercase tracking-widest font-bold text-gray-500 text-right">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody id="det-lista-items" class="divide-y divide-gray-100">
                            <!-- Los items se cargarán dinámicamente -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold border-t-2 border-fashion-black">
                                <td colspan="3" class="px-6 py-4 text-right uppercase tracking-widest text-xs">Total del
                                    Pedido</td>
                                <td class="px-6 py-4 text-right text-xl text-fashion-black" id="det-total-pedido">0.00 €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button onclick="cerrarModalDetallesPedido()"
                    class="bg-fashion-black text-white py-3 px-8 text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-all rounded-lg shadow-lg">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notificación -->

<div id="modal-notificacion" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
    style="z-index: 9999;">
    <div class="bg-white rounded-lg shadow-2xl w-[300px] p-6 text-center transform transition-all scale-100 relative">
        <div id="icono-notificacion" class="mb-4 text-4xl flex justify-center">
            <!-- Icono dinámico -->
        </div>
        <h3 id="titulo-notificacion" class="text-lg font-editorial italic text-fashion-black mb-2"></h3>
        <p id="mensaje-notificacion" class="text-gray-600 text-xs mb-6"></p>

        <button onclick="cerrarModalNotificacion()"
            class="w-full bg-fashion-black text-white py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors rounded">
            Entendido
        </button>
    </div>
</div>

<!-- Modal Confirmación Eliminar -->
<div id="modal-eliminacion" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
    style="z-index: 9999;">
    <div class="bg-white rounded-lg shadow-2xl w-[300px] p-6 text-center transform transition-all scale-100 relative">
        <div class="mb-4 text-4xl flex justify-center">
            <i class="ph ph-warning-circle text-red-500"></i>
        </div>
        <h3 class="text-lg font-editorial italic text-fashion-black mb-2">¿Estás seguro?</h3>
        <p id="mensaje-eliminacion" class="text-gray-600 text-xs mb-6"></p>

        <div class="flex gap-2 justify-center">
            <button onclick="cerrarModalEliminar()"
                class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-gray-300 transition-colors rounded">
                Cancelar
            </button>
            <button id="boton-confirmar-eliminacion" onclick="confirmarEliminacion()"
                class="flex-1 bg-red-600 text-white py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-red-700 transition-colors rounded">
                Eliminar
            </button>
        </div>
    </div>
</div>

<!-- Estilos para ocultar flechas de inputs numéricos -->
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<!-- Modal Producto (Crear/Editar) -->
<div id="modal-producto" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div style="max-width: 1200px;"
        class="bg-white rounded-xl shadow-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 flex justify-between items-center z-10">
            <div>
                <h2 id="titulo-modal-producto" class="font-editorial text-3xl italic text-fashion-black">Nuevo Producto
                </h2>
                <p class="text-gray-500 text-sm mt-1">Completa los detalles del producto</p>
            </div>
            <button onclick="cerrarModalProducto()"
                class="text-gray-400 hover:text-fashion-black transition-colors p-2 hover:bg-gray-100 rounded-full">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="formulario-producto" action="../modelos/productos/agregar-producto.php" method="POST"
            enctype="multipart/form-data" class="p-8">
            <input type="hidden" id="id-producto" name="product_id">
            <input type="hidden" id="accion-formulario-producto" name="action" value="create">

            <!-- Grid: Imagen | Datos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-8">

                <!-- Columna Izquierda: Imagen CUADRADA -->
                <div class="flex flex-col">
                    <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-3">
                        Imagen del Producto
                    </label>

                    <!-- Contenedor Cuadrado -->
                    <div id="zona-drop" class="relative w-full cursor-pointer group" style="padding-bottom: 100%;">
                        <input type="file" id="imagen-producto" name="imagen" accept="image/*" class="hidden"
                            onchange="previsualizarImagen(this)">

                        <!-- Área de Drop (llena todo el cuadrado) -->
                        <div id="contenedor-previsualizacion-imagen"
                            class="absolute inset-0 bg-gray-50 rounded-lg border-2 border-gray-200 flex items-center justify-center overflow-hidden transition-all group-hover:border-fashion-black">

                            <!-- Placeholder -->
                            <div id="placeholder-subida" class="text-center space-y-4">
                                <div
                                    class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-md mx-auto group-hover:scale-110 transition-transform">
                                    <i
                                        class="ph ph-upload-simple text-4xl text-gray-400 group-hover:text-fashion-black"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-fashion-black">Arrastra o selecciona una imagen
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2 uppercase tracking-wide">JPG, PNG, WEBP</p>
                                </div>
                            </div>

                            <!-- Preview Image (llena todo el cuadrado) -->
                            <img id="previsualizacion-imagen" src="#" alt="Vista previa"
                                class="hidden absolute inset-0 w-full h-full object-cover z-10">

                            <!-- Overlay Hover -->
                            <div id="capa-cambio-imagen"
                                class="hidden absolute inset-0 bg-black bg-opacity-50 items-center justify-center group-hover:flex z-20">
                                <p
                                    class="text-white font-semibold text-sm bg-black bg-opacity-60 px-4 py-2 rounded-full">
                                    Cambiar Imagen
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Datos del Producto -->
                <div class="flex flex-col gap-6 relative z-30 pointer-events-auto">

                    <!-- Nombre -->
                    <div class="space-y-2">
                        <label for="nombre-producto"
                            class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                            Nombre del Producto
                        </label>
                        <input type="text" id="nombre-producto" name="nombre" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-fashion-black focus:ring-0 transition-all text-fashion-black font-medium"
                            placeholder="Ej: Camiseta Básica Oversize">
                    </div>

                    <!-- Precio y Stock -->
                    <div class="grid grid-cols-2 gap-4">

                        <!-- Precio -->
                        <div class="space-y-2">
                            <label for="precio-producto"
                                class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                                Precio (€)
                            </label>
                            <div
                                class="flex items-center bg-gray-50 border border-gray-200 rounded-lg overflow-hidden focus-within:border-fashion-black transition-colors">
                                <input type="number" id="precio-producto" name="precio" required step="0.50" min="0"
                                    value="0.00"
                                    class="w-full bg-transparent border-none pl-4 pr-2 py-3 px-3 focus:ring-0 text-fashion-black font-bold appearance-none">
                                <div class="flex border-l border-gray-200">
                                    <button type="button" onclick="ajustarValor('precio-producto', -0.5)"
                                        class="p-2 hover:bg-gray-100 text-gray-500 border-r border-gray-100">
                                        <i class="ph ph-minus text-xs"></i>
                                    </button>
                                    <button type="button" onclick="ajustarValor('precio-producto', 0.5)"
                                        class="p-2 hover:bg-gray-100 text-gray-500">
                                        <i class="ph ph-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Descuento -->
                        <div class="space-y-2">
                            <label for="descuento-producto"
                                class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                                Descuento (%)
                            </label>
                            <div
                                class="flex items-center bg-gray-50 border border-gray-200 rounded-lg overflow-hidden focus-within:border-fashion-black transition-colors">
                                <input type="number" id="descuento-producto" name="descuento" value="0" min="0"
                                    max="100" step="1"
                                    class="w-full bg-transparent border-none pl-4 pr-2 py-3 px-3 focus:ring-0 text-fashion-black font-bold appearance-none">
                                <div
                                    class="flex border-l border-gray-200 px-3 bg-gray-100 text-gray-500 font-bold text-xs items-center justify-center">
                                    %
                                </div>
                            </div>
                        </div>

                        <!-- Tallas y Stock -->
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <!-- Stock Global -->
                            <div class="space-y-2">
                                <label for="stock-producto"
                                    class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                                    Stock Total
                                </label>
                                <input type="number" id="stock-producto" name="stock" value="0" min="0" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-fashion-black focus:ring-0 transition-all text-fashion-black font-bold appearance-none">
                            </div>

                            <!-- Tallas -->
                            <div class="space-y-2">
                                <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2">
                                    Tallas Disponibles <span class="text-red-500">*</span>
                                </label>
                                <div id="contenedor-tallas"
                                    class="space-y-3 p-4 bg-gray-50 rounded-lg border border-gray-200 relative z-50">
                                    <!-- Las filas de tallas se añadirán aquí dinámicamente -->
                                </div>
                                <button type="button" id="boton-añadir-talla"
                                    class="mt-2 text-xs font-bold uppercase tracking-widest text-fashion-black border border-fashion-black px-4 py-2 hover:bg-fashion-black hover:text-white transition-colors">
                                    + Añadir Talla
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div class="space-y-2">
                        <label for="id-categoria-producto"
                            class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                            Categoría
                        </label>
                        <select id="id-categoria-producto" name="categoria_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-fashion-black focus:ring-0 transition-all text-fashion-black cursor-pointer">
                            <option value="">Seleccionar Categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-2">
                        <label for="descripcion-producto"
                            class="block text-xs uppercase tracking-widest font-bold text-gray-500">
                            Descripción del Producto
                        </label>
                        <textarea id="descripcion-producto" name="descripcion" rows="6"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-fashion-black focus:ring-0 transition-all resize-none text-fashion-black"
                            placeholder="Describe los detalles, materiales, tallas y cuidados del producto..."></textarea>
                    </div>
                </div>
            </div>



            <!-- Botones -->
            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="button" onclick="cerrarModalProducto()"
                    class="flex-1 bg-gray-200 text-gray-700 py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all rounded-lg shadow-lg">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../animaciones/admin-dashboard.js?v=3.6"></script>
<?php include 'Footer.html'; ?>