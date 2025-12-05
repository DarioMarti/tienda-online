const LOGIN = document.getElementById('login');
const SEARCH = document.getElementById('search');
const LOGIN_SIDEBAR = document.getElementById('login-sidebar');
const CLOSE_LOGIN = document.getElementById('close-login');


window.addEventListener('scroll', function () {
    const topBar = document.getElementById('top-bar');
    const mainHeader = document.getElementById('main-header');

    if (window.scrollY > 30) {
        // Ocultar barra superior
        topBar.style.height = '0';
        topBar.style.padding = '0';
        topBar.style.opacity = '0';
        topBar.style.overflow = 'hidden';
        mainHeader.style.backgroundColor = 'white';
    } else {
        // Mostrar barra superior
        topBar.style.height = '';
        topBar.style.padding = '';
        topBar.style.opacity = '1';
        topBar.style.overflow = '';
    }
});

// Solo añadir eventos si el botón de login existe (usuario no logueado)
if (LOGIN) {
    LOGIN.addEventListener('click', function () {
        if (LOGIN_SIDEBAR.classList.contains('login-sidebar-open')) {
            LOGIN_SIDEBAR.classList.remove('login-sidebar-open');
            LOGIN_SIDEBAR.classList.add('login-sidebar-close');
            LOGIN.style.color = 'black';
        } else {
            LOGIN_SIDEBAR.classList.add('login-sidebar-open');
            LOGIN_SIDEBAR.classList.remove('login-sidebar-close');
            LOGIN.style.color = ' rgb(212 175 55 / var(--tw-text-opacity, 1))';
        }
    });
}

if (CLOSE_LOGIN) {
    CLOSE_LOGIN.addEventListener('click', function () {
        LOGIN_SIDEBAR.classList.remove('login-sidebar-open');
        LOGIN_SIDEBAR.classList.add('login-sidebar-close');
        if (LOGIN) LOGIN.style.color = 'black';
    });
}
