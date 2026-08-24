(() => {
    const mql = window.matchMedia('(prefers-color-scheme: dark)');
    function aplicar() {
        document.documentElement.setAttribute('data-bs-theme', mql.matches ? 'dark' : 'light');
    }
    aplicar();
    mql.addEventListener('change', aplicar);
})();
