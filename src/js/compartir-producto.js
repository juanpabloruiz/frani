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

    const abrirEstado = compartir.querySelector('[data-state-preview]');
    const dialogoEstado = compartir.querySelector('[data-state-dialog]');
    const compartirImagen = compartir.querySelector('[data-state-share]');
    const estadoImagen = compartir.querySelector('[data-state-status]');
    if (!abrirEstado || !dialogoEstado || typeof dialogoEstado.showModal !== 'function') return;

    let archivoEstado = null;
    let preparandoEstado = null;
    const puedeCompartirArchivos = window.isSecureContext
        && typeof navigator.share === 'function' && typeof navigator.canShare === 'function'
        && typeof File === 'function';

    const prepararImagen = async () => {
        if (!puedeCompartirArchivos || archivoEstado || preparandoEstado) return;
        estadoImagen.textContent = 'Preparando imagen para compartir…';
        preparandoEstado = (async () => {
            try {
                const respuesta = await fetch(dialogoEstado.dataset.image, {credentials: 'omit'});
                if (!respuesta.ok || !respuesta.headers.get('Content-Type')?.startsWith('image/jpeg')) {
                    throw new Error('Imagen no disponible');
                }
                const imagen = await respuesta.blob();
                if (imagen.size === 0) throw new Error('Imagen vacía');
                const archivo = new File([imagen], dialogoEstado.dataset.filename, {type: 'image/jpeg'});
                if (navigator.canShare({files: [archivo]})) {
                    archivoEstado = archivo;
                    compartirImagen.hidden = false;
                    estadoImagen.textContent = 'Compartí esta imagen o guardala en tu galería para subirla a Mi estado.';
                } else {
                    estadoImagen.textContent = 'Guardá la imagen en tu galería y elegila desde Mi estado de WhatsApp.';
                }
            } catch {
                estadoImagen.textContent = 'Podés descargar la imagen y subirla a Mi estado desde tu galería.';
            } finally {
                preparandoEstado = null;
            }
        })();
        await preparandoEstado;
    };

    abrirEstado.addEventListener('click', (evento) => {
        evento.preventDefault();
        if (!dialogoEstado.open) dialogoEstado.showModal();
        prepararImagen();
    });

    compartirImagen.addEventListener('click', async () => {
        if (!archivoEstado) return;
        compartirImagen.disabled = true;
        try {
            // Archivo listo antes del clic: conserva la activación del usuario en móviles.
            await navigator.share({files: [archivoEstado]});
        } catch (error) {
            if (error.name !== 'AbortError') {
                estadoImagen.textContent = 'Podés descargar la imagen y subirla a Mi estado desde tu galería.';
            }
        } finally {
            compartirImagen.disabled = false;
        }
    });
})();
