<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
require_once "../modelos/pedidos/mostrar-pedidos.php";
$pedidos = mostrarPedidos($usuario['id']);

$totalGastado = 0;
foreach ($pedidos as $pedido) {
    if ($pedido['estado'] !== 'cancelado') {
        $totalGastado += $pedido['coste_total'];
    }
}

include 'Cabecera.php';
?>

<!-- PÁGINA DE PERFIL -->
<main class="min-h-screen bg-fashion-gray py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- Encabezado del Perfil -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="font-editorial text-4xl italic text-fashion-black mb-2">
                        <?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellidos']) ?>
                    </h1>
                    <p class="text-gray-600 text-sm uppercase tracking-widest">
                        <?= htmlspecialchars($usuario['email']) ?>
                    </p>
                </div>
                <span
                    class="px-3 py-1 <?php if (in_array($usuario['rol'], ['admin', 'empleado'])): ?> bg-fashion-accent text-white <?php else: ?> bg-gray-100 text-gray-600 <?php endif; ?> text-xs uppercase tracking-wider rounded-full">
                    <?= htmlspecialchars($usuario['rol']) ?>
                </span>
            </div>
        </div>

        <!-- Grid de 2 columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Columna Izquierda: Información Personal -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Datos Personales -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-editorial text-2xl italic text-fashion-black">Datos Personales</h2>
                        <button id="edit-profile-btn"
                            class="text-xs uppercase tracking-widest text-fashion-black hover:text-fashion-accent transition-colors font-semibold">
                            <i class="ph ph-pencil-simple mr-2"></i>Editar
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="space-y-2">
                            <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Nombre</label>
                            <div
                                class="w-full px-4 py-3 bg-fashion-gray border border-gray-200 rounded-lg text-fashion-black">
                                <?= htmlspecialchars($usuario['nombre']) ?>
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="space-y-2">
                            <label
                                class="text-xs uppercase tracking-widest font-semibold text-gray-500">Apellidos</label>
                            <div
                                class="w-full px-4 py-3 bg-fashion-gray border border-gray-400 rounded-lg text-fashion-black">
                                <?= htmlspecialchars($usuario['apellidos']) ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="text-xs uppercase tracking-widest font-semibold text-gray-500">Email</label>
                            <div
                                class="w-full px-4 py-3 bg-fashion-gray border border-gray-200 rounded-lg text-fashion-black">
                                <?= htmlspecialchars($usuario['email']) ?>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="space-y-2">
                            <label
                                class="text-xs uppercase tracking-widest font-semibold text-gray-500">Teléfono</label>
                            <div
                                class="w-full px-4 py-3 bg-fashion-gray border border-gray-200 rounded-lg text-fashion-black">
                                <?= htmlspecialchars($usuario['telefono'] ?: 'No especificado') ?>
                            </div>
                        </div>

                        <!-- Dirección (ocupa 2 columnas) -->
                        <div class="space-y-2 md:col-span-2">
                            <label
                                class="text-xs uppercase tracking-widest font-semibold text-gray-500">Dirección</label>
                            <div
                                class="w-full px-4 py-3 bg-fashion-gray border border-gray-200 rounded-lg text-fashion-black min-h-[80px]">
                                <?= htmlspecialchars($usuario['direccion'] ?: 'No especificada') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Pedidos -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-editorial text-2xl italic text-fashion-black">Mis Pedidos</h2>
                        <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">
                            <i class="ph ph-package mr-2"></i><?= count($pedidos) ?> Pedidos
                        </span>

                    </div>

                    <!-- Lista de Pedidos -->
                    <?php if (empty($pedidos)): ?>
                        <div class="text-center py-12">
                            <i class="ph ph-shopping-bag text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-sm uppercase tracking-widest">No tienes pedidos aún</p>
                            <a href="index.php"
                                class="inline-block mt-4 text-fashion-black hover:text-fashion-accent transition-colors text-xs uppercase tracking-widest font-semibold underline underline-offset-4">
                                Explorar Productos
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                                            ID</th>
                                        <th class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                                            Fecha</th>
                                        <th class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                                            Estado</th>
                                        <th
                                            class="px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-500 text-right">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-fashion-black">#<?= $pedido['id'] ?></td>
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                <?= date('d/m/Y', strtotime($pedido['fecha'])) ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider 
                                                    <?= $pedido['estado'] === 'entregado' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' ?>">
                                                    <?= $pedido['estado'] ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-fashion-black">
                                                <?= number_format($pedido['coste_total'], 2) ?>€
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>

            </div>

            <!-- Columna Derecha: Acciones Rápidas -->
            <div class="space-y-6">

                <!-- Cambiar Contraseña -->
                <div class="bg-white rounded-lg shadow-xl p-6">
                    <h3 class="font-editorial text-xl italic text-fashion-black mb-4">Seguridad</h3>
                    <button id="btnCambiarContrasena"
                        class="w-full bg-fashion-gray text-fashion-black py-3 px-4 text-xs uppercase tracking-widest font-semibold hover:bg-fashion-black hover:text-white transition-all duration-300 rounded-lg">
                        <i class="ph ph-lock-key mr-2"></i>Cambiar Contraseña
                    </button>
                </div>

                <!-- Preferencias -->
                <div class="bg-white rounded-lg shadow-xl p-6">
                    <h3 class="font-editorial text-xl italic text-fashion-black mb-4">Preferencias</h3>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm text-gray-700">Recibir Newsletter</span>
                            <input type="checkbox"
                                class="rounded border-gray-300 text-fashion-black focus:ring-fashion-black">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm text-gray-700">Notificaciones de Pedidos</span>
                            <input type="checkbox" checked
                                class="rounded border-gray-300 text-fashion-black focus:ring-fashion-black">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm text-gray-700">Ofertas Exclusivas</span>
                            <input type="checkbox"
                                class="rounded border-gray-300 text-fashion-black focus:ring-fashion-black">
                        </label>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="bg-white rounded-lg shadow-xl p-6">
                    <h3 class="font-editorial text-xl italic text-fashion-black mb-4">Resumen</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-xs uppercase tracking-widest text-gray-500">Total Pedidos</span>
                            <span class="text-2xl font-bold text-fashion-black"><?= count($pedidos) ?></span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-xs uppercase tracking-widest text-gray-500">Total Gastado</span>
                            <span
                                class="text-2xl font-bold text-fashion-black"><?= number_format($totalGastado, 2) ?>€</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs uppercase tracking-widest text-gray-500">Miembro Desde</span>
                            <span class="text-sm text-gray-600">
                                <?= htmlspecialchars(date('Y-m-d', strtotime($usuario['fecha_creacion']))) ?>

                        </div>
                    </div>
                </div>

                <!-- Cerrar Sesión -->
                <div class="bg-white rounded-lg shadow-xl p-6">
                    <a href="../modelos/usuarios/cerrar-sesion.php"
                        class="block w-full bg-red-600 text-white py-3 px-4 text-xs uppercase tracking-widest font-semibold hover:bg-red-700 transition-all duration-300 rounded-lg text-center">
                        <i class="ph ph-sign-out mr-2"></i>Cerrar Sesión
                    </a>
                </div>

                <!-- Eliminar Cuenta -->

                <a href="#"
                    class="block w-full py-3 px-4 text-xs uppercase tracking-widest font-semibold bg-gray-100 hover:bg-gray-200 text-red-600 transition-all duration-300 rounded-lg text-center"
                    id="eliminar-cuenta">
                    <i class="ph ph-trash mr-2"></i>Eliminar Cuenta
                </a>

            </div>

        </div>

    </div>
</main>

<!-- MODAL DE EDICIÓN -->
<div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header del Modal -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center">
            <h2 class="font-editorial text-3xl italic text-fashion-black">Editar Perfil</h2>
            <button id="close-modal" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- Formulario -->
        <form id="edit-profile-form" class="p-8" action="../modelos/usuarios/modificar-usuario.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors">
                </div>

                <!-- Apellidos -->
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors">
                </div>

                <!-- Email (solo lectura) -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Email</label>
                    <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" disabled
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-500 italic">El email no se puede modificar</p>
                </div>

                <!-- Teléfono -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Teléfono</label>
                    <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors"
                        placeholder="+34 600 000 000">
                </div>

                <!-- Dirección -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Dirección</label>
                    <textarea name="direccion" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors resize-none"
                        placeholder="Calle, número, ciudad, código postal..."><?= htmlspecialchars($usuario['direccion']) ?></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 mt-8">
                <button type="button" id="cancel-btn"
                    class="flex-1 bg-gray-200 text-gray-700 py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all duration-300 rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-4 px-8 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DE CAMBIO DE CONTRASEÑA -->
<div id="change-password-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <!-- Header del Modal -->
        <div
            class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center rounded-t-lg">
            <h2 class="font-editorial text-2xl italic text-fashion-black">Cambiar Contraseña</h2>
            <button id="close-password-modal" class="text-gray-400 hover:text-fashion-black transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <!-- Formulario -->
        <form id="change-password-form" class="p-8" action="../modelos/usuarios/cambiar-contrasena.php" method="POST">
            <div class="space-y-4">
                <!-- Contraseña Actual -->
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Contraseña
                        Actual</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors">
                </div>

                <!-- Nueva Contraseña -->
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Nueva
                        Contraseña</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors">
                </div>

                <!-- Confirmar Nueva Contraseña -->
                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest font-semibold text-gray-700">Confirmar Nueva
                        Contraseña</label>
                    <input type="password" name="confirm_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-fashion-black focus:outline-none focus:border-fashion-black transition-colors">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 mt-8">
                <button type="button" id="cancel-password-btn"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 px-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all duration-300 rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-fashion-black text-white py-3 px-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DE RESULTADO (Éxito/Error) -->
<div id="result-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-8 text-center transform transition-all">
        <!-- Icono -->
        <div id="result-icon" class="mb-4">
            <!-- Se llenará dinámicamente con JavaScript -->
        </div>

        <!-- Título -->
        <h3 id="result-title" class="font-editorial text-2xl italic text-fashion-black mb-2">
            <!-- Se llenará dinámicamente -->
        </h3>

        <!-- Mensaje -->
        <p id="result-message" class="text-gray-600 mb-6">
            <!-- Se llenará dinámicamente -->
        </p>

        <!-- Botón Cerrar -->
        <button id="close-result-modal"
            class="w-full bg-fashion-black text-white py-3 px-6 text-xs uppercase tracking-widest font-semibold hover:bg-fashion-accent transition-all duration-300 rounded-lg">
            Cerrar
        </button>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
<div id="delete-account-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-8 text-center transform transition-all">
        <!-- Icono -->
        <div class="mb-4">
            <i class="ph ph-warning-circle text-6xl text-red-500"></i>
        </div>

        <!-- Título -->
        <h3 class="font-editorial text-2xl italic text-fashion-black mb-2">
            ¿Estás seguro?
        </h3>

        <!-- Mensaje -->
        <p class="text-gray-600 mb-6">
            Esta acción eliminará permanentemente tu cuenta y no se puede deshacer.
        </p>

        <!-- Botones -->
        <div class="flex gap-4">
            <button id="cancel-delete-btn"
                class="flex-1 bg-gray-200 text-gray-700 py-3 px-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-gray-300 transition-all duration-300 rounded-lg">
                Cancelar
            </button>
            <button id="confirm-delete-btn"
                class="flex-1 bg-red-600 text-white py-3 px-4 text-xs uppercase tracking-[0.25em] font-semibold hover:bg-red-700 transition-all duration-300 rounded-lg shadow-lg hover:shadow-xl">
                Eliminar
            </button>
        </div>
    </div>
</div>


<script>
    // Pasar datos de pedidos a JS
    const userOrders = <?php echo json_encode($pedidos); ?>;
</script>
<script src="../animaciones/perfil-Usuario.js"></script>
<?php
include 'Footer.html';
?>