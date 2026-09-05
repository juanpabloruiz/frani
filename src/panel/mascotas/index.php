<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();

$editando = false;
$mascota = [
    'id' => '', 'tipo' => '', 'talle' => '', 'precio' => ''
];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare(
        "SELECT id, tipo, talle, precio
        FROM mascotas WHERE id = ?"
    );
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $mascota = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($mascota) {
        $editando = true;
    }
}

$consulta = $db->query(
    "SELECT id, tipo, talle, precio, creado, modificado
    FROM mascotas
    ORDER BY tipo ASC"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container-fluid">
        <div class="row g-4">

            <!-- Columna izquierda: Formulario -->
            <div class="col-md-4" style="position: sticky; top: 84px; align-self: flex-start;">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="<?= e(base_path('panel/mascotas/' . ($editando ? 'actualizar' : 'insertar') . ($editando ? '#mascota-' . $mascota['id'] : ''))) ?>">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $mascota['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <input type="text" name="tipo" class="form-control" required
                                    value="<?= e($mascota['tipo']) ?>" placeholder="Ej. Perro, Gato, Loro...">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Talle</label>
                                    <input type="text" name="talle" class="form-control"
                                        value="<?= e($mascota['talle'] ?? '') ?>" placeholder="Ej. M, L, I, II...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="precio" class="form-control" required
                                            value="<?= e(numero_limpio($mascota['precio'])) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editando ? 'Actualizar mascota' : 'Guardar mascota' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-header text-center fw-bold">Talles por letra</div>
                            <table class="table table-bordered table-sm mb-0 text-center">
                                <thead>
                                    <tr class="table-secondary">
                                        <th>Talle</th>
                                        <th>Largo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td style="color: #dc3545; font-weight: 600;">XXS</td><td>27</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">XS</td><td>32</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">S</td><td>35</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">M</td><td>41</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">L</td><td>43</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">XL</td><td>48</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">XXL</td><td>51</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-header text-center fw-bold">Talle largo</div>
                            <table class="table table-bordered table-sm mb-0 text-center">
                                <thead>
                                    <tr class="table-secondary">
                                        <th>Talle</th>
                                        <th>Largo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td style="color: #dc3545; font-weight: 600;">0</td><td>26</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">1</td><td>30</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">2</td><td>34</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">3</td><td>37</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">4</td><td>43</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">5</td><td>47</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">6</td><td>50</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">7</td><td>53</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">8</td><td>56</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">9</td><td>62</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">10</td><td>69</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">11</td><td>77</td></tr>
                                    <tr><td style="color: #dc3545; font-weight: 600;">12</td><td>81</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Tabla -->
            <div class="col-md-8">
                <div style="position: sticky; top: 76px; z-index: 10; background: white; padding: 16px; border-bottom: 1px solid #dee2e6; margin-bottom: 16px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorMascotas" class="form-control" placeholder="Buscar mascota...">
                    </div>
                </div>

                <div id="contenedorTabla" class="card shadow-sm" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                    <table class="table table-hover table-bordered mb-0" id="tablaMascotas">
                    <thead class="text-center">
                        <tr class="align-middle">
                            <th scope="col" style="width: 50px;">#</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Talle</th>
                            <th scope="col" style="white-space: nowrap; width: 120px;">Precio</th>
                            <th scope="col" style="white-space: nowrap;">Creado</th>
                            <th scope="col" style="white-space: nowrap;">Modificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $token = CSRF_token(); ?>
                        <?php while ($fila = $consulta->fetch_assoc()): ?>
                            <tr id="mascota-<?= e((string) $fila['id']) ?>" class="align-middle <?= $editando && (int) $fila['id'] === (int) $mascota['id'] ? 'table-active' : '' ?>"
                                style="cursor: pointer;"
                                data-edit="<?= e(base_path('panel/mascotas?id=' . $fila['id'])) ?>">
                                <td class="text-center">
                                    <form method="POST" action="<?= e(base_path('panel/mascotas/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta mascota?');">
                                        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                        <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation();"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                                <td class="bg-success text-white"><?= e($fila['tipo']) ?></td>
                                <td class="text-center"><?= e($fila['talle'] ?? '') ?></td>
                                <td class="text-center bg-success text-white" style="white-space: nowrap;"><?= e(moneda($fila['precio'])) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= e(date('d-m | H:i', strtotime($fila['creado']))) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= $fila['modificado'] ? e(date('d-m | H:i', strtotime($fila['modificado']))) : '' ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($consulta->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary">No hay mascotas cargadas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const buscador = document.getElementById('buscadorMascotas');
        const filas = document.querySelectorAll('#tablaMascotas tbody tr');

        function normalizar(texto) {
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        buscador.addEventListener('input', function () {
            const termino = normalizar(this.value);
            filas.forEach(fila => {
                const texto = normalizar(fila.textContent);
                fila.style.display = texto.includes(termino) ? '' : 'none';
            });
        });

        filas.forEach(fila => {
            fila.addEventListener('click', function (e) {
                if (e.target.closest('form')) return;
                window.location.href = this.dataset.edit;
            });
        });

        <?php if ($editando): ?>
        window.addEventListener('load', () => {
            const fila = document.getElementById('mascota-<?= e((string) $mascota['id']) ?>');
            const contenedor = document.getElementById('contenedorTabla');
            if (fila && contenedor) {
                contenedor.scrollTop = fila.offsetTop;
            }
        });
        <?php endif; ?>

    </script>
</body>

</html>