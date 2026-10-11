(() => {
    const revisiones = new WeakMap();
    const organizar = async grilla => {
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
        await Promise.all(cargas);
        if (revisiones.get(grilla) !== revision || grilla.hidden || !window.Masonry) return;
        const masonry = Masonry.data(grilla);
        if (masonry) {
            masonry.reloadItems();
            masonry.layout();
        } else {
            new Masonry(grilla, { percentPosition: true });
        }
    };

    document.querySelectorAll('[data-catalog-search]').forEach(buscador => {
        const input = buscador.querySelector('input');
        const limpiar = buscador.querySelector('[data-search-clear]');
        const estado = buscador.querySelector('[data-search-status]');
        const grilla = document.getElementById(input.getAttribute('aria-controls'));
        if (!grilla) return;
        const detalle = buscador.dataset.detail ? document.getElementById(buscador.dataset.detail) : null;
        const paginacion = buscador.dataset.pagination ? document.getElementById(buscador.dataset.pagination) : null;
        const mas = paginacion?.querySelector('[data-load-more]');
        const estadoCarga = paginacion?.querySelector('[data-load-status]');
        const inicial = grilla.innerHTML;
        const siguienteInicial = paginacion?.dataset.next ? Number(paginacion.dataset.next) : null;
        const totalInicial = Number(paginacion?.dataset.total || 0);
        let siguiente = siguienteInicial;
        let terminoActivo = '';
        let temporizador;
        let solicitud;
        let revision = 0;
        let cargando = false;
        let pendiente = false;
        let falloCarga = false;
        let observador;

        const bloquearMas = bloquear => {
            if (!mas) return;
            mas.classList.toggle('disabled', bloquear);
            mas.setAttribute('aria-disabled', String(bloquear));
        };

        const configurarPagina = (proxima, total, mostrarFin = true) => {
            if (!paginacion) return;
            siguiente = proxima;
            paginacion.hidden = total === 0 || (siguiente === null && !mostrarFin);
            mas.hidden = siguiente === null;
            mas.textContent = 'Ver más productos';
            estadoCarga.textContent = siguiente === null && total > 0 ? 'No hay más productos.' : '';
            if (siguiente !== null) mas.href = `?pagina=${Math.floor(siguiente / 25) + 1}`;
            bloquearMas(false);
        };

        const planificarCarga = () => {
            if (!observador) return;
            requestAnimationFrame(() => {
                if (!paginacion.hidden && paginacion.getBoundingClientRect().top <= window.innerHeight + 400) {
                    cargarMas();
                }
            });
        };

        const mostrar = async (html, buscando = true, anexar = false) => {
            if (anexar) {
                const existentes = new Set(Array.from(grilla.querySelectorAll('.product-card-link'))
                    .map(enlace => enlace.getAttribute('href')));
                const plantilla = document.createElement('template');
                plantilla.innerHTML = html;
                Array.from(plantilla.content.children).forEach(tarjeta => {
                    const enlace = tarjeta.querySelector('.product-card-link')?.getAttribute('href');
                    if (!enlace || existentes.has(enlace)) tarjeta.remove();
                    else existentes.add(enlace);
                });
                grilla.append(plantilla.content);
            } else {
                if (window.Masonry) Masonry.data(grilla)?.destroy();
                grilla.innerHTML = html;
            }
            if (detalle) {
                grilla.hidden = !buscando;
                detalle.hidden = buscando;
            }
            await organizar(grilla);
        };

        const buscar = async (termino, actual, offset = 0, anexar = false) => {
            const controlador = new AbortController();
            solicitud = controlador;
            cargando = true;
            bloquearMas(true);
            const url = new URL(buscador.dataset.endpoint, window.location.origin);
            url.searchParams.set('busqueda', termino);
            if (buscador.dataset.category) url.searchParams.set('categoria', buscador.dataset.category);
            if (paginacion) url.searchParams.set('offset', String(offset));
            try {
                const respuesta = await fetch(url, { signal: controlador.signal });
                if (!respuesta.ok) throw new Error('No se pudo buscar');
                const datos = await respuesta.json();
                if (actual !== revision) return;
                if (typeof datos.html !== 'string' || !Number.isInteger(datos.total) || datos.total < 0
                    || (paginacion && datos.siguiente !== null
                        && (!Number.isInteger(datos.siguiente) || datos.siguiente <= offset))) {
                    throw new Error('Respuesta de catálogo inválida');
                }
                await mostrar(datos.html, true, anexar);
                if (actual !== revision) return;
                terminoActivo = termino;
                falloCarga = false;
                configurarPagina(datos.siguiente, datos.total);
                estado.textContent = termino
                    ? (datos.total === 1 ? '1 producto encontrado.' : `${datos.total} productos encontrados.`)
                    : '';
            } catch (error) {
                if (error.name === 'AbortError' || actual !== revision) return;
                if (anexar) {
                    falloCarga = true;
                    estadoCarga.textContent = 'No se pudieron cargar más productos. Podés reintentar.';
                    mas.textContent = 'Reintentar carga';
                } else {
                    estado.textContent = 'No se pudo buscar. Intentá nuevamente.';
                }
            } finally {
                if (actual === revision) {
                    cargando = false;
                    pendiente = false;
                    bloquearMas(false);
                    grilla.setAttribute('aria-busy', 'false');
                    if (!falloCarga) planificarCarga();
                }
            }
        };

        const cargarMas = () => {
            if (!paginacion || paginacion.hidden || siguiente === null || cargando || pendiente || falloCarga) return;
            estadoCarga.textContent = 'Cargando más productos...';
            grilla.setAttribute('aria-busy', 'true');
            buscar(terminoActivo, revision, siguiente, true);
        };

        const actualizar = () => {
            clearTimeout(temporizador);
            solicitud?.abort();
            const actual = ++revision;
            const termino = input.value.trim();
            cargando = false;
            pendiente = true;
            falloCarga = false;
            if (paginacion) paginacion.hidden = true;
            limpiar.hidden = input.value.length === 0;
            if (!termino) {
                terminoActivo = '';
                configurarPagina(siguienteInicial, totalInicial, false);
                estado.textContent = '';
                grilla.setAttribute('aria-busy', 'false');
                mostrar(inicial, false).then(() => {
                    if (actual !== revision) return;
                    pendiente = false;
                    planificarCarga();
                });
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
        if (paginacion) {
            mas.addEventListener('click', evento => {
                evento.preventDefault();
                falloCarga = false;
                cargarMas();
            });
            if ('IntersectionObserver' in window) {
                observador = new IntersectionObserver(entradas => {
                    if (entradas.some(entrada => entrada.isIntersecting)) cargarMas();
                }, { rootMargin: '400px 0px' });
                organizar(grilla).then(() => {
                    observador.observe(paginacion);
                    planificarCarga();
                });
            } else {
                organizar(grilla);
            }
        } else {
            organizar(grilla);
        }
    });
})();
