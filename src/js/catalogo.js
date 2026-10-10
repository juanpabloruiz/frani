(() => {
    const revisiones = new WeakMap();
    const organizar = grilla => {
        if (grilla.hidden) return;
        const revision = (revisiones.get(grilla) || 0) + 1;
        revisiones.set(grilla, revision);
        const cargas = Array.from(grilla.querySelectorAll('img')).map(imagen => {
            if (imagen.complete) return Promise.resolve();
            return new Promise(resolver => {
                imagen.addEventListener('load', resolver, { once: true });
                imagen.addEventListener('error', resolver, { once: true });
            });
        });
        Promise.all(cargas).then(() => {
            if (revisiones.get(grilla) !== revision || grilla.hidden || !window.Masonry) return;
            const masonry = Masonry.data(grilla);
            if (masonry) {
                masonry.reloadItems();
                masonry.layout();
            } else {
                new Masonry(grilla, { percentPosition: true });
            }
        });
    };

    document.querySelectorAll('[data-catalog-search]').forEach(buscador => {
        const input = buscador.querySelector('input');
        const limpiar = buscador.querySelector('[data-search-clear]');
        const estado = buscador.querySelector('[data-search-status]');
        const grilla = document.getElementById(input.getAttribute('aria-controls'));
        if (!grilla) return;
        const detalle = buscador.dataset.detail ? document.getElementById(buscador.dataset.detail) : null;
        const inicial = grilla.innerHTML;
        let temporizador;
        let solicitud;
        let revision = 0;

        const mostrar = (html, buscando = true) => {
            if (window.Masonry) Masonry.data(grilla)?.destroy();
            grilla.innerHTML = html;
            if (detalle) {
                grilla.hidden = !buscando;
                detalle.hidden = buscando;
            }
            organizar(grilla);
        };

        const buscar = async (termino, actual) => {
            solicitud = new AbortController();
            const url = new URL(buscador.dataset.endpoint, window.location.origin);
            url.searchParams.set('busqueda', termino);
            if (buscador.dataset.category) url.searchParams.set('categoria', buscador.dataset.category);
            try {
                const respuesta = await fetch(url, { signal: solicitud.signal });
                if (!respuesta.ok) throw new Error('No se pudo buscar');
                const datos = await respuesta.json();
                if (actual !== revision) return;
                mostrar(datos.html);
                estado.textContent = datos.total === 1 ? '1 producto encontrado.' : `${datos.total} productos encontrados.`;
            } catch (error) {
                if (error.name === 'AbortError' || actual !== revision) return;
                estado.textContent = 'No se pudo buscar. Intentá nuevamente.';
            } finally {
                if (actual === revision) grilla.setAttribute('aria-busy', 'false');
            }
        };

        const actualizar = () => {
            clearTimeout(temporizador);
            solicitud?.abort();
            const actual = ++revision;
            const termino = input.value.trim();
            limpiar.hidden = input.value.length === 0;
            if (!termino) {
                mostrar(inicial, false);
                estado.textContent = '';
                grilla.setAttribute('aria-busy', 'false');
                return;
            }
            estado.textContent = 'Buscando productos...';
            grilla.setAttribute('aria-busy', 'true');
            temporizador = setTimeout(() => buscar(termino, actual), 200);
        };
        input.addEventListener('input', actualizar);
        limpiar.addEventListener('click', () => {
            input.value = '';
            actualizar();
            input.focus();
        });
    });
    document.querySelectorAll('.masonry-grid').forEach(organizar);
})();
