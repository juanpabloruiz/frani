(() => {
    const normalizar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    document.querySelectorAll('[data-search-input]').forEach(lista => {
        const buscador = document.getElementById(lista.dataset.searchInput);
        const filtro = lista.dataset.filterInput ? document.getElementById(lista.dataset.filterInput) : null;
        const items = Array.from(lista.querySelectorAll('[data-card-item]'));
        const vacio = document.createElement('p');
        vacio.className = 'sb-vacio col-12';
        vacio.textContent = filtro
            ? 'No hay resultados con los filtros seleccionados.'
            : 'No hay resultados para esta búsqueda.';
        vacio.hidden = true;
        lista.append(vacio);

        const aplicar = () => {
            const termino = normalizar(buscador?.value ?? '');
            const categoria = filtro?.value ?? '';
            items.forEach(item => {
                const coincideTexto = normalizar(item.textContent).includes(termino);
                const coincideCategoria = !categoria || item.dataset.categoria === categoria;
                item.hidden = !(coincideTexto && coincideCategoria);
            });
            lista.querySelectorAll('[data-card-group]').forEach(grupo => {
                grupo.hidden = !Array.from(grupo.querySelectorAll('[data-card-item]')).some(item => !item.hidden);
            });
            vacio.hidden = !items.length || items.some(item => !item.hidden);
        };

        buscador?.addEventListener('input', aplicar);
        filtro?.addEventListener('change', aplicar);
    });
    document.querySelectorAll('.sb-card[data-edit]').forEach(card => {
        const esControl = target => target.closest('form, a, button, input, select, textarea, label');
        card.addEventListener('click', event => {
            if (!esControl(event.target)) window.location.href = card.dataset.edit;
        });
        card.addEventListener('keydown', event => {
            if (event.target !== card || !['Enter', ' '].includes(event.key)) return;
            event.preventDefault();
            window.location.href = card.dataset.edit;
        });
    });
    const activo = document.querySelector('.sb-card.activo');
    if (activo && window.matchMedia('(min-width: 768px)').matches) {
        activo.scrollIntoView({ block: 'center' });
    }
})();
