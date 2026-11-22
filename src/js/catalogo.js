(() => {
    document.querySelectorAll('[data-catalog-search]').forEach(buscador => {
        const input = buscador.querySelector('input');
        const limpiar = buscador.querySelector('[data-search-clear]');
        const estado = buscador.querySelector('[data-search-status]');
        const grilla = document.getElementById(input.getAttribute('aria-controls'));
        if (!grilla) return;

        const tamanoPagina = Number(buscador.dataset.pageSize) || 25;
        const detalle = buscador.dataset.detail ? document.getElementById(buscador.dataset.detail) : null;
        const paginacion = buscador.dataset.pagination ? document.getElementById(buscador.dataset.pagination) : null;
        const estadoPaginacion = paginacion?.querySelector('[data-pagination-status]');
        const inicial = grilla.innerHTML;
        const paginacionInicial = paginacion?.innerHTML || '';
        const paginaInicial = Number(paginacion?.dataset.page) || 1;
        const totalInicial = Number(paginacion?.dataset.total || 0);
        let paginaActual = paginaInicial;
        let terminoActivo = '';
        let temporizador;
        let solicitud;
        let revision = 0;

        const configurarPaginacion = (html, total) => {
            if (!paginacion) return;
            paginacion.innerHTML = html;
            paginacion.dataset.total = String(total);
            paginacion.dataset.page = String(paginaActual);
            paginacion.hidden = total <= tamanoPagina;
            if (estadoPaginacion) {
                const paginas = Math.max(1, Math.ceil(total / tamanoPagina));
                estadoPaginacion.textContent = total > 0
                    ? `Página ${paginaActual} de ${paginas} · ${total} productos`
                    : '';
            }
        };

        const mostrar = (html, buscando = true) => {
            grilla.innerHTML = html;
            if (detalle) {
                grilla.hidden = !buscando;
                detalle.hidden = buscando;
            }
        };

        const buscar = async (termino, actual, pagina) => {
            const controlador = new AbortController();
            solicitud = controlador;
            grilla.setAttribute('aria-busy', 'true');
            const url = new URL(buscador.dataset.endpoint, window.location.origin);
            url.searchParams.set('busqueda', termino);
            if (buscador.dataset.category) url.searchParams.set('categoria', buscador.dataset.category);
            if (paginacion) {
                url.searchParams.set('offset', String((pagina - 1) * tamanoPagina));
                url.searchParams.set('por_pagina', String(tamanoPagina));
            }
            try {
                const respuesta = await fetch(url, { signal: controlador.signal });
                if (!respuesta.ok) throw new Error('No se pudo buscar');
                const datos = await respuesta.json();
                if (actual !== revision) return;
                if (typeof datos.html !== 'string' || !Number.isInteger(datos.total) || datos.total < 0
                    || (paginacion && typeof datos.paginacion !== 'string')) {
                    throw new Error('Respuesta de catálogo inválida');
                }
                mostrar(datos.html, true);
                if (actual !== revision) return;
                paginaActual = pagina;
                terminoActivo = termino;
                configurarPaginacion(datos.paginacion, datos.total);
                estado.textContent = termino
                    ? (datos.total === 1 ? '1 producto encontrado.' : `${datos.total} productos encontrados.`)
                    : '';
            } catch (error) {
                if (error.name === 'AbortError' || actual !== revision) return;
                estado.classList.remove('visually-hidden');
                estado.textContent = 'No se pudo buscar. Intentá nuevamente.';
            } finally {
                if (actual === revision) grilla.setAttribute('aria-busy', 'false');
            }
        };

        const actualizar = () => {
            clearTimeout(temporizador);
            solicitud?.abort();
            const actual = ++revision;
            estado.classList.add('visually-hidden');
            const termino = input.value.trim();
            limpiar.hidden = input.value.length === 0;
            if (!termino) {
                terminoActivo = '';
                paginaActual = paginaInicial;
                mostrar(inicial, false);
                configurarPaginacion(paginacionInicial, totalInicial);
                estado.textContent = '';
                grilla.setAttribute('aria-busy', 'false');
                return;
            }
            estado.textContent = 'Buscando productos...';
            grilla.setAttribute('aria-busy', 'true');
            temporizador = setTimeout(() => buscar(termino, actual, 1), 200);
        };

        input.addEventListener('input', actualizar);
        limpiar.addEventListener('click', () => {
            input.value = '';
            actualizar();
            input.focus();
        });

        // Sin búsqueda activa los enlaces navegan normalmente (?pagina=N).
        // Con búsqueda activa se solicita sólo la página indicada por AJAX.
        paginacion?.addEventListener('click', evento => {
            const enlace = evento.target.closest('[data-pagina]');
            if (!enlace || !terminoActivo) return;
            const pagina = Number(enlace.dataset.pagina);
            if (!Number.isInteger(pagina) || pagina < 1) return;
            evento.preventDefault();
            const actual = ++revision;
            solicitud?.abort();
            buscar(terminoActivo, actual, pagina);
        });
    });
})();
