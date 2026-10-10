(() => {
    const compartir = document.querySelector('[data-product-share]');
    if (!compartir) return;

    const datos = {title: compartir.dataset.title, url: compartir.dataset.url};
    const copiar = compartir.querySelector('[data-copy-link]');
    const nativo = compartir.querySelector('[data-native-share]');
    const estado = compartir.querySelector('[data-share-status]');
    const alternativa = compartir.querySelector('[data-share-fallback]');
    const enlace = alternativa.querySelector('input');

    const informar = (mensaje) => {
        estado.textContent = mensaje;
        estado.hidden = false;
    };

    const mostrarEnlace = () => {
        informar('Copiá este enlace para compartir el producto:');
        alternativa.hidden = false;
        enlace.focus();
        enlace.select();
    };

    copiar.hidden = false;
    copiar.addEventListener('click', async () => {
        alternativa.hidden = true;
        if (!navigator.clipboard?.writeText) {
            mostrarEnlace();
            return;
        }
        copiar.disabled = true;
        try {
            await navigator.clipboard.writeText(datos.url);
            informar('Enlace copiado. Podés pegarlo donde quieras.');
        } catch {
            mostrarEnlace();
        } finally {
            copiar.disabled = false;
        }
    });

    if (window.isSecureContext && typeof navigator.share === 'function') {
        nativo.hidden = false;
        nativo.addEventListener('click', async () => {
            estado.hidden = true;
            alternativa.hidden = true;
            nativo.disabled = true;
            try {
                await navigator.share(datos);
            } catch (error) {
                if (error.name !== 'AbortError') mostrarEnlace();
            } finally {
                nativo.disabled = false;
            }
        });
    }
})();
