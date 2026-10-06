/* Tema claro / oscuro segun la preferencia del sistema operativo.
 *
 * Bootstrap 5.3 ya trae el modo oscuro, pero no lo activa solo: espera que
 * alguien ponga data-bs-theme="dark" en <html>. Este script lo hace.
 *
 * Va en el <head> sin defer ni async a proposito. Si se cargara al final
 * o asincronico, el navegador pintaria primero la pagina en claro y
 * recien despues saltaria a oscuro.
 */
(function () {
    var raiz = document.documentElement;
    var preferencia = window.matchMedia('(prefers-color-scheme: dark)');

    function aplicar() {
        raiz.setAttribute('data-bs-theme', preferencia.matches ? 'dark' : 'light');
    }

    aplicar();

    // Si el usuario cambia el tema del sistema con la pagina abierta,
    // seguirlo sin recargar.
    preferencia.addEventListener('change', aplicar);
})();