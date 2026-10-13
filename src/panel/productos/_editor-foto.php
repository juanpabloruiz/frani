<div class="modal" id="editorFoto" tabindex="-1" aria-labelledby="editorFotoTitulo" aria-describedby="editorFotoAyuda" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="editorFotoTitulo"><i class="fa-solid fa-crop-simple me-2" aria-hidden="true"></i>Recortar foto</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar editor"></button>
            </div>
            <div class="modal-body">
                <p id="editorFotoAyuda" class="text-body-secondary small">Arrastrá los bordes para ajustar el recorte. Podés mover la foto y hacer zoom con el mouse o con dos dedos.</p>
                <div class="foto-editor-superficie rounded" tabindex="0" role="group" aria-label="Área de recorte. Usá las flechas para mover la foto y las teclas más y menos para el zoom.">
                    <img id="fotoEditorImagen" alt="Imagen para recortar">
                </div>
                <div class="d-flex flex-wrap align-items-end gap-3 mt-3">
                    <div class="flex-grow-1 foto-editor-proporcion">
                        <label for="fotoProporcion" class="form-label small mb-1">Proporción</label>
                        <select id="fotoProporcion" class="form-select form-select-sm" data-editor-control disabled>
                            <option value="libre">Libre</option>
                            <option value="1">Cuadrada (1:1)</option>
                            <option value="1.3333333333333333">Horizontal (4:3)</option>
                            <option value="0.75">Vertical (3:4)</option>
                        </select>
                    </div>
                    <div class="btn-group" role="group" aria-label="Zoom">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-editor-accion="alejar" data-editor-control disabled aria-label="Alejar" title="Alejar"><i class="fa-solid fa-magnifying-glass-minus" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-editor-accion="acercar" data-editor-control disabled aria-label="Acercar" title="Acercar"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i></button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Orientación y recorte">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-editor-accion="girar" data-editor-control disabled><i class="fa-solid fa-rotate-right me-1" aria-hidden="true"></i>Girar</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-editor-accion="restablecer" data-editor-control disabled>Restablecer</button>
                    </div>
                </div>
                <p class="small text-body-secondary mt-2 mb-0" id="fotoEditorEstado" role="status">Cargando imagen…</p>
                <div class="alert alert-danger py-2 mt-2 mb-0" id="fotoEditorError" role="alert" hidden></div>
            </div>
            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-outline-secondary me-auto" id="fotoUsarCompleta" data-editor-control disabled>Usar imagen completa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="fotoAplicarRecorte" data-editor-control disabled>Aplicar recorte</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= e(base_path('vendor/cropperjs/cropper.min.js')) ?>"></script>
<script src="<?= e(base_path('js/editor-foto.js?v=1')) ?>"></script>
