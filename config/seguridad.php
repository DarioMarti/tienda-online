<?php
/**
 * SISTEMA DE SEGURIDAD Y CONTROL DE ACCESO
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario actual tiene rol de administrador o empleado.
 * @return bool
 */
function esPersonalAutorizado()
{
    return isset($_SESSION['usuario']) &&
        isset($_SESSION['usuario']['rol']) &&
        in_array($_SESSION['usuario']['rol'], ['admin', 'empleado']);
}

/**
 * Verifica si el usuario actual es administrador.
 * @return bool
 */
function esAdmin()
{
    return isset($_SESSION['usuario']) &&
        isset($_SESSION['usuario']['rol']) &&
        $_SESSION['usuario']['rol'] === 'admin';
}

/**
 * Verifica si el usuario está logueado.
 * @return bool
 */
function estaLogueado()
{
    return isset($_SESSION['usuario']);
}

/**
 * Restringe el acceso en endpoints de API (retorna JSON) para administradores/empleados.
 */
function restringirAccesoAPI()
{
    if (!esPersonalAutorizado()) {
        if (ob_get_length())
            ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. No tienes permisos suficientes.']);
        exit();
    }
}

/**
 * Restringe el acceso en endpoints de API (retorna JSON) SOLO para administradores.
 */
function restringirSoloAdminAPI()
{
    if (!esAdmin()) {
        if (ob_get_length())
            ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Solo administradores pueden realizar esta acción.']);
        exit();
    }
}

/**
 * Restringe el acceso en endpoints de API (retorna JSON) para cualquier usuario logueado.
 */
function restringirInvitadosAPI()
{
    if (!estaLogueado()) {
        if (ob_get_length())
            ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Debes iniciar sesión.']);
        exit();
    }
}

/**
 * Restringe el acceso en páginas para administradores/empleados (redirige al index).
 */
function restringirAccesoPagina($urlRedireccion = 'index.php')
{
    if (!esPersonalAutorizado()) {
        header("Location: $urlRedireccion");
        exit();
    }
}

/**
 * Restringe el acceso a usuarios no logueados (redirige al index).
 */
function restringirInvitados($urlRedireccion = 'index.php')
{
    if (!estaLogueado()) {
        header("Location: $urlRedireccion");
        exit();
    }
}
?>