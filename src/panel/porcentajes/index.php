<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

$toastExito = $_SESSION['toast_exito'] ?? null;
unset($_SESSION['toast_exito']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porcentajes | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">

        <?php if ($toastExito): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($toastExito) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <!-- Bloque 1: Cambio de precio por categoría -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-percent me-2"></i>Cambiar precio por categoría</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_path('panel/porcentajes/aplicar')) ?>" id="formPorcentaje">
                    <?= CSRF_field() ?>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-select" required>
                                <option value="">Seleccionar categoría...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= (int) $cat['id'] ?>"><?= e($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="porcentaje" class="form-label">Porcentaje</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="porcentaje" id="porcentaje"
                                    class="form-control" placeholder="Ej: 25 o -10" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-check me-1"></i>Cambiar porcentaje
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Usá valores positivos para subir (ej: 25) y negativos para bajar (ej: -10).
                    </small>
                </form>
            </div>
        </div>

        <!-- Bloque 2: Calculadora de porcentaje -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-calculator me-2"></i>Calculadora de porcentaje</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="calc_precio" class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" id="calc_precio" class="form-control"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="calc_porcentaje" class="form-label">Porcentaje</label>
                        <div class="input-group">
                            <input type="number" step="0.01" id="calc_porcentaje" class="form-control"
                                placeholder="Ej: -20">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Resultado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="calc_resultado" class="form-control bg-success text-white fw-bold"
                                readonly value="0.00">
                        </div>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Ingresá el precio y el porcentaje (negativo para descuento). El resultado se calcula al instante.
                </small>
            </div>
        </div>

    </div>

    <script src="<?= e(base_path('../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const calcPrecio = document.getElementById('calc_precio');
        const calcPorcentaje = document.getElementById('calc_porcentaje');
        const calcResultado = document.getElementById('calc_resultado');

        function calcularPorcentaje() {
            const precio = parseFloat(calcPrecio.value) || 0;
            const porcentaje = parseFloat(calcPorcentaje.value) || 0;
            const resultado = precio * (1 + porcentaje / 100);
            calcResultado.value = resultado.toFixed(2);
        }

        calcPrecio.addEventListener('input', calcularPorcentaje);
        calcPorcentaje.addEventListener('input', calcularPorcentaje);
    </script>

</body>

</html>
