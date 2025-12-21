<?php
session_start();
require("../modelos/usuarios/mostrar-usuarios.php");
require("../modelos/categorias/mostrar-categoria.php");
// Verificación de seguridad: Solo administradores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}


// Obtener usuarios para la gestión
$usuarios = mostrarUsuarios();
$categorias = mostrarCategorias();

include 'Cabecera.php';
?>

<div class="min-h-screen bg-fashion-gray flex">

    <!-- Sidebar de Navegación -->
    <aside class="w-96 bg-white shadow-xl hidden md:block fixed h-full z-10 pt-20">
        <div class="p-6">
            <h2 class="font-editorial text-2xl italic text-fashion-black mb-8">Panel Admin</h2>
            <nav class="space-y-2">
                <button onclick="switchTab('dashboard')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-fashion-black bg-fashion-gray transition-colors"
                    data-tab="dashboard">
                    <i class="ph ph-squares-four mr-2"></i>Dashboard
                </button>
                <button onclick="switchTab('pedidos')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="pedidos">
                    <i class="ph ph-package mr-2"></i>Pedidos
                </button>
                <button onclick="switchTab('productos')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="productos">
                    <i class="ph ph-t-shirt mr-2"></i>Productos
                </button>
                <button onclick="switchTab('categorias')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="categorias">
                    <i class="ph ph-tag mr-2"></i>Categorías
                </button>
                <button onclick="switchTab('usuarios')"
                    class="nav-item w-full text-left px-4 py-3 rounded-lg text-sm uppercase tracking-widest font-semibold text-gray-500 hover:bg-fashion-gray hover:text-fashion-black transition-colors"
                    data-tab="usuarios">
                    <i class="ph ph-users mr-2"></i>Usuarios
                </button>
            </nav>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="flex-1 md:ml-96 p-8 pt-24">

        <!-- Sección Dashboard (Resumen) -->
        <section id="dashboard-section" class="tab-content block">
            <h1 class="font-editorial text-4xl italic text-fashion-black mb-8">Resumen General</h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Cards de Resumen -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Total Ventas</p>
                            <h3 class="text-2xl font-bold text-fashion-black">0€</h3>
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
                            <h3 class="text-2xl font-bold text-fashion-black">0</h3>
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
                            <h3 class="text-2xl font-bold text-fashion-black">0</h3>
                        </div>
                        <div class="p-2 bg-orange-100 rounded-full text-orange-600">
                            <i class="ph ph-tag text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Usuarios -->
        <section id="usuarios-section" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Usuarios</h1>
                <button onclick="openUserModal()"
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
                                    Nombre</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Email</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">Rol
                                </th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Estado</th>
                                <th class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500">
                                    Fecha Registro</th>
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($usuarios as $user): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-fashion-black">
                                            <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider 
                                        <?= $user['rol'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                            <?= htmlspecialchars($user['rol']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider 
                                        <?= $user['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= $user['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= date('d M Y', strtotime($user['fecha_creacion'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <?php if ($user['id'] !== $_SESSION['usuario']['id']): ?>
                                            <button onclick='editUser(<?= json_encode($user) ?>)'
                                                class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                                <i class="ph ph-pencil-simple text-xl"></i>
                                            </button>
                                            <button
                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre']) ?>')"
                                                class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                                <i class="ph ph-trash text-xl"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">Tú</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Secciones Placeholder con Botones PEDIDOS -->
        <section id="pedidos-section" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Pedidos</h1>
                <button onclick="openOrderModal()"
                    class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                    <i class="ph ph-plus mr-2"></i>Nuevo Pedido
                </button>
            </div>
            <div class="bg-white p-12 rounded-lg shadow-xl text-center">
                <i class="ph ph-package text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 uppercase tracking-widest">Próximamente</p>
            </div>
        </section>

        <section id="productos-section" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Productos</h1>
                <button onclick="openProductModal()"
                    class="bg-fashion-black text-white px-6 py-3 rounded-lg text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors shadow-lg">
                    <i class="ph ph-plus mr-2"></i>Nuevo Producto
                </button>
            </div>
            <div class="bg-white p-12 rounded-lg shadow-xl text-center">
                <i class="ph ph-t-shirt text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 uppercase tracking-widest">Próximamente</p>
            </div>
        </section>

        <section id="categorias-section" class="tab-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="font-editorial text-4xl italic text-fashion-black">Gestión de Categorías</h1>
                <button onclick="openCategoryModal()"
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
                                <th
                                    class="px-6 py-4 text-xs uppercase tracking-widest font-semibold text-gray-500 text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($categorias as $categoria): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-fashion-black">
                                            <?= htmlspecialchars($categoria['nombre']) ?>
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
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button onclick='editCategory(<?= json_encode($categoria) ?>)'
                                            class="text-gray-400 hover:text-fashion-black transition-colors" title="Editar">
                                            <i class="ph ph-pencil-simple text-xl"></i>
                                        </button>
                                        <button
                                            onclick="deleteCategory(<?= $categoria['id'] ?>, '<?= htmlspecialchars($categoria['nombre']) ?>')"
                                            class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                            <i class="ph ph-trash text-xl"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

    </main>
</div>

<!-- Modal Usuario (Crear/Editar) -->
<div id="user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 id="modal-title" class="font-editorial text-3xl italic text-fashion-black">Nuevo Usuario</h2>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="user-form" action="../modelos/usuarios/admin-save-user.php" method="POST" class="p-8">
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="action" id="form_action" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>
                <div class="space-y-2 md:col-span-2" id="password-group">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Contraseña</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                    <p class="text-xs text-gray-500 italic mt-1" id="password-hint">Dejar en blanco para mantener la
                        actual al editar.</p>
                </div>
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Rol</label>
                    <select name="rol" id="rol"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black bg-white">
                        <option value="cliente">Cliente</option>
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
                <button type="button" onclick="closeUserModal()"
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
<div id="category-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 id="category-modal-title" class="font-editorial text-3xl italic text-fashion-black">Nueva Categoría</h2>
            <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="category-form" action="../modelos/categorias/crear-categoria.php" method="POST" class="p-8">
            <input type="hidden" name="category_id" id="category_id">
            <input type="hidden" name="action" id="category_form_action" value="create">

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="cat_nombre" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black">
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Descripción</label>
                    <textarea name="descripcion" id="cat_descripcion" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-fashion-black"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Categoría Padre</label>
                    <select name="categoria_padre_id" id="cat_parent_id"
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
                <button type="button" onclick="closeCategoryModal()"
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

<!-- Modal Notificación -->
<!-- Modal Notificación -->
<div id="notification-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
    style="z-index: 9999;">
    <div class="bg-white rounded-lg shadow-2xl w-[300px] p-6 text-center transform transition-all scale-100 relative">
        <div id="notification-icon" class="mb-4 text-4xl flex justify-center">
            <!-- Icono dinámico -->
        </div>
        <h3 id="notification-title" class="text-lg font-editorial italic text-fashion-black mb-2"></h3>
        <p id="notification-message" class="text-gray-600 text-xs mb-6"></p>

        <button onclick="closeNotificationModal()"
            class="w-full bg-fashion-black text-white py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-colors rounded">
            Entendido
        </button>
    </div>
</div>

<!-- Modal Confirmación Eliminar -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" style="z-index: 9999;">
    <div class="bg-white rounded-lg shadow-2xl w-[300px] p-6 text-center transform transition-all scale-100 relative">
        <div class="mb-4 text-4xl flex justify-center">
            <i class="ph ph-warning-circle text-red-500"></i>
        </div>
        <h3 class="text-lg font-editorial italic text-fashion-black mb-2">¿Estás seguro?</h3>
        <p id="delete-message" class="text-gray-600 text-xs mb-6"></p>
        
        <div class="flex gap-2 justify-center">
            <button onclick="closeDeleteModal()" 
                class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-gray-300 transition-colors rounded">
                Cancelar
            </button>
            <button onclick="confirmDelete()" 
                class="flex-1 bg-red-600 text-white py-2 px-4 text-[10px] uppercase tracking-widest font-semibold hover:bg-red-700 transition-colors rounded">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script src="../animaciones/admin-dashboard.js"></script>
<?php include 'Footer.html'; ?>