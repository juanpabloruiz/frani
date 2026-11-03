(() => {
    'use strict';

    const widget = document.querySelector('[data-foto-cliente]');
    if (!widget) return;

    const input = widget.querySelector('input[type="file"]');
    const preview = widget.querySelector('[data-foto-preview]');
    const placeholder = widget.querySelector('[data-foto-placeholder]');
    const savedSource = preview.getAttribute('src') || '';
    let selectedSource = null;

    function render(file) {
        if (selectedSource) URL.revokeObjectURL(selectedSource);
        selectedSource = file ? URL.createObjectURL(file) : null;
        const source = selectedSource || savedSource;
        if (source) preview.src = source;
        else preview.removeAttribute('src');
        preview.hidden = !source;
        placeholder.hidden = Boolean(source);
    }

    input.addEventListener('change', () => render(input.files[0]));
    input.form.addEventListener('reset', () => render(null));
})();
