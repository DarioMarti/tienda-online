
// Lógica para filtrar por tallas
// Se asume que 'tallasSeleccionadas' se define globalmente en la vista antes de cargar este script

function toggleTalla(talla, btnElement) {
    if (typeof window.tallasSeleccionadas === 'undefined') {
        window.tallasSeleccionadas = [];
    }

    // Asegurarse de que es un array (en caso de que PHP mandara un objeto o string por error)
    if (!Array.isArray(window.tallasSeleccionadas)) {
        window.tallasSeleccionadas = Object.values(window.tallasSeleccionadas);
    }

    const index = window.tallasSeleccionadas.indexOf(talla.toString());

    if (index === -1) {
        window.tallasSeleccionadas.push(talla.toString());
        btnElement.classList.remove('bg-white', 'text-black', 'border-gray-200');
        btnElement.classList.add('bg-black', 'text-white', 'border-black');
    } else {
        window.tallasSeleccionadas.splice(index, 1);
        btnElement.classList.remove('bg-black', 'text-white', 'border-black');
        btnElement.classList.add('bg-white', 'text-black', 'border-gray-200');
    }
}

function aplicarFiltros() {
    const url = new URL(window.location.href);

    // Eliminar parámetros viejos de talla (soporta tanto 'tallas' como 'tallas[]')
    url.searchParams.delete('tallas[]');
    url.searchParams.delete('tallas');

    if (Array.isArray(window.tallasSeleccionadas)) {
        window.tallasSeleccionadas.forEach(talla => {
            url.searchParams.append('tallas[]', talla);
        });
    }

    const priceSlider = document.getElementById('filter-price-slider');
    if (priceSlider) {
        url.searchParams.set('precio', priceSlider.value);
    }

    // Resetear página al filtrar para evitar que se quede en una página vacía
    url.searchParams.delete('pagina');

    window.location.href = url.toString();
}
