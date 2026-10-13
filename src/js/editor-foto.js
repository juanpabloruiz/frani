(() => {
    'use strict';

    const widget = document.querySelector('[data-foto-producto]');
    const modalElement = document.getElementById('editorFoto');
    if (!widget || !modalElement) return;

    const input = widget.querySelector('input[type="file"]');
    const form = input.form;
    const preview = widget.querySelector('#imgPreview');
    const placeholder = widget.querySelector('#placeholderPreview');
    const editButton = widget.querySelector('[data-foto-recortar]');
    const restoreButton = widget.querySelector('[data-foto-restaurar]');
    const errorBox = widget.querySelector('[data-foto-error]');
    const status = widget.querySelector('[data-foto-estado]');
    const image = modalElement.querySelector('#fotoEditorImagen');
    const surface = modalElement.querySelector('.foto-editor-superficie');
    const proportion = modalElement.querySelector('#fotoProporcion');
    const applyButton = modalElement.querySelector('#fotoAplicarRecorte');
    const wholeButton = modalElement.querySelector('#fotoUsarCompleta');
    const editorError = modalElement.querySelector('#fotoEditorError');
    const editorStatus = modalElement.querySelector('#fotoEditorEstado');
    const controls = modalElement.querySelectorAll('[data-editor-control]');
    const savedSource = preview.getAttribute('src') || '';
    const maxBytes = 5 * 1024 * 1024;
    const supported = typeof window.Cropper === 'function'
        && typeof window.bootstrap?.Modal === 'function'
        && typeof window.DataTransfer === 'function';
    const modal = supported ? new bootstrap.Modal(modalElement) : null;

    // Keep the source separate from the exported file so a second crop retains
    // the full photo until the product is saved.
    const initialState = () => ({
        source: savedSource, sourceFile: null, file: null,
        preview: savedSource, data: null, ratio: 'libre'
    });
    let committed = initialState();
    let pending = null;
    let cropper = null;
    let ready = false;
    let busy = false;

    function showError(message, target = errorBox) {
        target.textContent = message;
        target.hidden = !message;
    }

    function setFile(file) {
        const transfer = new DataTransfer();
        if (file) transfer.items.add(file);
        input.files = transfer.files;
    }

    function release(state, keep) {
        if (!state) return;
        const retained = new Set(keep ? [keep.source, keep.preview] : []);
        new Set([state.source, state.preview]).forEach(url => {
            if (url?.startsWith('blob:') && !retained.has(url)) URL.revokeObjectURL(url);
        });
    }

    function render() {
        preview.src = committed.preview;
        preview.hidden = !committed.preview;
        placeholder.hidden = Boolean(committed.preview);
        editButton.hidden = !supported || !committed.source;
        restoreButton.hidden = !supported || !committed.file;
    }

    function enableControls(enabled) {
        controls.forEach(control => { control.disabled = !enabled; });
    }

    function commit(state, message) {
        setFile(state.file);
        release(committed, state);
        committed = state;
        render();
        status.textContent = message;
        showError('');
        pending = null;
        modal?.hide();
    }

    function open(state) {
        pending = { ...state };
        ready = false;
        enableControls(false);
        proportion.value = pending.ratio;
        editorStatus.textContent = 'Cargando imagen…';
        showError('', editorError);
        image.src = pending.source;
        modal.show();
    }

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) {
            if (supported) setFile(committed.file);
            return;
        }
        const validType = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)
            || (!file.type && /\.(jpe?g|png|webp)$/i.test(file.name));
        if (!validType || file.size > maxBytes) {
            if (supported) setFile(committed.file);
            else {
                input.value = '';
                release(committed);
                committed = initialState();
                render();
            }
            showError(!validType ? 'Elegí una imagen JPG, PNG o WebP.' : 'La imagen debe pesar hasta 5 MB.');
            return;
        }
        showError('');
        const source = URL.createObjectURL(file);
        const state = { source, sourceFile: file, file, preview: source, data: null, ratio: 'libre' };
        if (supported) {
            // The input must contain only a confirmed photo, even if the form
            // is submitted by keyboard while the editor is open.
            setFile(committed.file);
            open(state);
        } else {
            release(committed, state);
            committed = state;
            render();
        }
    });

    if (!supported) {
        render();
        return;
    }

    editButton.addEventListener('click', () => open(committed));
    restoreButton.addEventListener('click', () => {
        release(committed);
        committed = initialState();
        setFile(null);
        render();
        showError('');
        status.textContent = savedSource ? 'Se conserva la foto guardada.' : 'Se quitó la foto seleccionada.';
    });

    image.addEventListener('error', () => {
        if (!pending) return;
        ready = false;
        enableControls(false);
        editorStatus.textContent = '';
        showError('No se pudo abrir esta imagen. Cancelá y elegí otra foto.', editorError);
    });

    modalElement.addEventListener('shown.bs.modal', () => {
        if (!pending) return;
        cropper = new Cropper(image, {
            viewMode: 1,
            dragMode: 'move',
            aspectRatio: proportion.value === 'libre' ? NaN : Number(proportion.value),
            autoCropArea: 0.9,
            data: pending.data,
            minContainerWidth: 200,
            minContainerHeight: 200,
            toggleDragModeOnDblclick: false,
            ready() {
                ready = true;
                enableControls(true);
                editorStatus.textContent = 'El recorte se guardará al guardar el producto.';
                surface.focus({ preventScroll: true });
            },
            zoom(event) {
                if (event.detail.ratio > 8) event.preventDefault();
            }
        });
    });

    modalElement.addEventListener('hide.bs.modal', event => {
        if (busy) event.preventDefault();
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        cropper?.destroy();
        cropper = null;
        ready = false;
        release(pending, committed);
        pending = null;
        image.removeAttribute('src');
        setFile(committed.file);
        // Bootstrap restores focus for data-toggle triggers only; these open
        // programmatically, so restore focus explicitly.
        (committed.source ? editButton : input).focus({ preventScroll: true });
    });

    proportion.addEventListener('change', () => {
        if (!ready || busy) return;
        cropper.setAspectRatio(proportion.value === 'libre' ? NaN : Number(proportion.value));
    });

    modalElement.querySelectorAll('[data-editor-accion]').forEach(button => {
        button.addEventListener('click', () => {
            if (!ready || busy) return;
            switch (button.dataset.editorAccion) {
                case 'acercar': cropper.zoom(0.1); break;
                case 'alejar': cropper.zoom(-0.1); break;
                case 'girar': cropper.rotate(90); break;
                case 'restablecer':
                    proportion.value = 'libre';
                    cropper.setAspectRatio(NaN);
                    cropper.reset();
                    break;
            }
        });
    });

    surface.addEventListener('keydown', event => {
        if (!ready || busy) return;
        const step = event.shiftKey ? 20 : 5;
        const moves = { ArrowLeft: [-step, 0], ArrowRight: [step, 0], ArrowUp: [0, -step], ArrowDown: [0, step] };
        if (moves[event.key]) {
            event.preventDefault();
            cropper.move(...moves[event.key]);
        } else if (['+', '=', '-'].includes(event.key)) {
            event.preventDefault();
            cropper.zoom(event.key === '-' ? -0.1 : 0.1);
        }
    });

    wholeButton.addEventListener('click', () => {
        if (!ready || busy) return;
        const state = { ...pending, file: pending.sourceFile, preview: pending.source, data: null, ratio: 'libre' };
        commit(state, state.file ? 'Foto lista. Guardá el producto para aplicarla.' : 'Se conserva la foto guardada.');
    });

    applyButton.addEventListener('click', async () => {
        if (!ready || busy) return;
        busy = true;
        enableControls(false);
        showError('', editorError);
        editorStatus.textContent = 'Preparando recorte…';
        try {
            const data = cropper.getData();
            if (data.width < 1 || data.height < 1) throw new Error('Ajustá el área de recorte.');
            const scale = Math.min(1, 800 / data.width, 800 / data.height);
            const canvas = cropper.getCroppedCanvas({
                width: Math.max(1, Math.round(data.width * scale)),
                height: Math.max(1, Math.round(data.height * scale)),
                maxWidth: 800, maxHeight: 800,
                fillColor: '#fff', imageSmoothingEnabled: true, imageSmoothingQuality: 'high'
            });
            if (!canvas?.width || !canvas.height) throw new Error('No se pudo preparar el recorte.');
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.92));
            if (!blob || blob.size > maxBytes) throw new Error('No se pudo preparar el recorte. Probá con otra imagen.');
            const name = (pending.sourceFile?.name || 'producto.jpg').replace(/\.[^.]+$/, '') + '-recortada.jpg';
            const file = new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
            const state = { ...pending, file, preview: URL.createObjectURL(file), data, ratio: proportion.value };
            busy = false;
            commit(state, 'Recorte listo. Guardá el producto para aplicarlo.');
        } catch (error) {
            showError(error.message || 'No se pudo preparar el recorte. Probá de nuevo.', editorError);
            editorStatus.textContent = '';
        } finally {
            busy = false;
            enableControls(ready);
        }
    });

    form.addEventListener('submit', event => {
        if (pending || busy) event.preventDefault();
    });
    render();
})();
