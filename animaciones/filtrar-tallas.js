
// Lógica para filtrar por tallas
// Se asume que 'tallasSeleccionadas' se define globalmente en la vista antes de cargar este script

function toggleTalla(talla, btnElement) {
    // Asegurarnos de que existe la variable global, si no, inicializarla
    if (typeof tallasSeleccionadas === 'undefined') {
        console.error("Error: tallasSeleccionadas no está definido.");
        return;
    }

    const index = tallasSeleccionadas.indexOf(talla);

    if (index === -1) {
        // Si no está, la añadimos
        tallasSeleccionadas.push(talla);
        // Actualizar UI - Activo
        btnElement.classList.remove('bg-white', 'text-black', 'border-gray-200');
        btnElement.classList.add('bg-black', 'text-white', 'border-black');
    } else {
        // Si está, la quitamos
        tallasSeleccionadas.splice(index, 1);
        // Actualizar UI - Inactivo
        btnElement.classList.remove('bg-black', 'text-white', 'border-black');
        btnElement.classList.add('bg-white', 'text-black', 'border-gray-200');
    }

    console.log("Tallas seleccionadas:", tallasSeleccionadas);
}

function aplicarFiltros() {
    // Construir URL con parámetros
    const url = new URL(window.location.href);

    // Limpiar params anteriores de talla para reconstruirlos
    url.searchParams.delete('tallas[]');

    // Añadir nuevos params de tallas
    if (typeof tallasSeleccionadas !== 'undefined') {
        tallasSeleccionadas.forEach(talla => {
            url.searchParams.append('tallas[]', talla);
        });
    }

    // Añadir param de precio
    const priceSlider = document.getElementById('filter-price-slider');
    if (priceSlider) {
        url.searchParams.set('precio', priceSlider.value);
    }

    // Mantener el orden si existe (ya está en window.location.href, pero por seguridad)
    // Si se quisiera resetear paginación, etc, este es el sitio.

    // Recargar página
    window.location.href = url.toString();
}
