<?php $fotoProducto = $producto['foto'] ?? ''; ?>
<div data-foto-producto>
    <label for="fotoInput" class="form-label">Foto del producto</label>
    <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" aria-describedby="fotoAyuda fotoEstado">
    <div id="fotoAyuda" class="form-text">JPG, PNG o WebP, hasta 5 MB. Podés recortar la foto antes de guardar.</div>
    <div class="alert alert-danger py-2 mt-2 mb-0" data-foto-error role="alert" hidden></div>

    <div id="vistaPrevia" class="foto-producto-preview border rounded p-2 text-center mt-3">
        <img id="imgPreview" src="<?= !empty($fotoProducto) ? e(base_path('img/productos/' . $fotoProducto . '.jpg')) : '' ?>" alt="Vista previa de la foto del producto" <?= empty($fotoProducto) ? 'hidden' : '' ?>>
        <p id="placeholderPreview" class="text-body-secondary mb-0 py-4" <?= !empty($fotoProducto) ? 'hidden' : '' ?>>Sin imagen</p>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-2">
        <button type="button" class="btn btn-outline-primary btn-sm" data-foto-recortar hidden>
            <i class="fa-solid fa-crop-simple me-1" aria-hidden="true"></i>Recortar foto
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-foto-restaurar hidden>Deshacer cambios de foto</button>
    </div>
    <p id="fotoEstado" class="form-text mb-0" data-foto-estado role="status"></p>
</div>
