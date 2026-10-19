/* Conserva el select como fuente del ID y de los eventos de la venta. */
function activarBuscadorProducto(select) {
    const contenedor = document.createElement('div');
    contenedor.className = 'position-relative';
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.id = select.id;
    select.removeAttribute('id');
    input.placeholder = 'Buscar producto…';
    input.autocomplete = 'off';
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');
    const lista = document.createElement('div');
    lista.id = `${input.id}_resultados`;
    lista.className = 'list-group position-absolute w-100 shadow bg-body';
    lista.style.cssText = 'z-index:1050;max-height:260px;overflow-y:auto;';
    lista.setAttribute('role', 'listbox');
    lista.setAttribute('aria-label', 'Productos encontrados');
    lista.hidden = true;
    input.setAttribute('aria-controls', lista.id);
    select.before(contenedor);
    select.hidden = true;
    contenedor.append(input, select, lista);
    let opciones = [];
    let activo = -1;
    const normalizar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();

    function cerrar() {
        lista.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        activo = -1;
    }

    function resaltar(indice) {
        activo = indice;
        Array.from(lista.children).forEach((elemento, i) => {
            elemento.classList.toggle('active', i === activo);
            if (elemento.getAttribute('role') === 'option') {
                elemento.setAttribute('aria-selected', String(i === activo));
            }
        });
        if (activo >= 0) {
            input.setAttribute('aria-activedescendant', lista.children[activo].id);
            lista.children[activo].scrollIntoView({ block: 'nearest' });
        } else {
            input.removeAttribute('aria-activedescendant');
        }
    }

    function elegir(indice) {
        if (!opciones[indice]) return;
        select.value = opciones[indice].value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function mostrar() {
        const consulta = select.value ? '' : normalizar(input.value);
        opciones = Array.from(select.options).filter(opcion => opcion.value && normalizar(opcion.textContent).includes(consulta));
        lista.replaceChildren();
        opciones.forEach((opcion, indice) => {
            const resultado = document.createElement('div');
            resultado.id = `${lista.id}_${indice}`;
            resultado.className = 'list-group-item list-group-item-action';
            resultado.style.cursor = 'pointer';
            resultado.setAttribute('role', 'option');
            resultado.textContent = opcion.textContent;
            resultado.addEventListener('mousedown', evento => evento.preventDefault());
            resultado.addEventListener('click', () => elegir(indice));
            lista.append(resultado);
        });
        if (!opciones.length) {
            const mensaje = document.createElement('div');
            mensaje.className = 'list-group-item text-body-secondary';
            mensaje.setAttribute('role', 'status');
            mensaje.textContent = 'No se encontraron productos';
            lista.append(mensaje);
        }
        lista.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        resaltar(opciones.length ? 0 : -1);
    }

    select.addEventListener('change', () => {
        input.value = select.value ? select.selectedOptions[0].textContent : '';
        input.setCustomValidity('');
        cerrar();
    });
    input.addEventListener('focus', mostrar);
    input.addEventListener('click', () => {
        if (lista.hidden) mostrar();
    });
    input.addEventListener('input', () => {
        const consulta = input.value;
        if (select.value) {
            select.value = '';
            select.dispatchEvent(new Event('change', { bubbles: true }));
            input.value = consulta;
        }
        input.setCustomValidity(input.value.trim() ? 'Seleccioná un producto de la lista.' : '');
        mostrar();
    });
    input.addEventListener('keydown', evento => {
        if (evento.isComposing) return;
        if (evento.key === 'ArrowDown' || evento.key === 'ArrowUp') {
            evento.preventDefault();
            if (lista.hidden) {
                mostrar();
                if (evento.key === 'ArrowUp' && opciones.length) resaltar(opciones.length - 1);
            } else if (opciones.length) {
                resaltar((activo + (evento.key === 'ArrowDown' ? 1 : -1) + opciones.length) % opciones.length);
            }
        } else if (evento.key === 'Enter') {
            evento.preventDefault();
            if (!lista.hidden) elegir(activo);
        } else if (evento.key === 'Escape') {
            evento.preventDefault();
            cerrar();
        } else if (evento.key === 'Tab') {
            cerrar();
        }
    });
    input.addEventListener('blur', cerrar);
}
