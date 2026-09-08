</main>
<footer class="container-fluid bg-dark text-white text-center py-5">
    <p class="mb-0">
        Derechos reservados Frani - <?= date('Y') ?><br>
        Corrientes - Argentina
    </p>
</footer>

<script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(base_path('js/masonry.pkgd.min.js')) ?>"></script>
<script>
    document.querySelectorAll('.masonry-grid').forEach((grilla) => {
        const imagenes = Array.from(grilla.querySelectorAll('img'));
        const cargas = imagenes.map((imagen) => {
            if (imagen.complete) return Promise.resolve();

            return new Promise((resolver) => {
                imagen.addEventListener('load', resolver, { once: true });
                imagen.addEventListener('error', resolver, { once: true });
            });
        });

        Promise.all(cargas).then(() => {
            new Masonry(grilla, { percentPosition: true });
        });
    });
</script>
</body>

</html>
