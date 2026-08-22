<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$usuario = usuario_actual();
$db = conexion();

$totalProductos = $db->query("SELECT COUNT(*) FROM productos")->fetch_row()[0];
$totalCategorias = $db->query("SELECT COUNT(*) FROM categorias")->fetch_row()[0];
$totalFacturas = $db->query("SELECT COUNT(*) FROM facturas")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../css/estilo.css?v=5')) ?>">
    <script src="<?= e(base_path('../js/chart.js')) ?>"></script>
</head>

<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <div class="container">
        <!-- Estadísticas -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-chart-bar me-2"></i>Estadísticas</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-light active" data-periodo="dia">Día</button>
                    <button type="button" class="btn btn-outline-light" data-periodo="semana">Semana</button>
                    <button type="button" class="btn btn-outline-light" data-periodo="mes">Mes</button>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px;">
                    <canvas id="graficoFacturas"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/productos')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-box fa-3x text-primary mb-3"></i>
                        <h2 class="h4"><?= $totalProductos ?></h2>
                        <p class="text-secondary mb-0"><?= $totalProductos == 1 ? 'Producto' : 'Productos' ?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/categorias')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-tags fa-3x text-success mb-3"></i>
                        <h2 class="h4"><?= $totalCategorias ?></h2>
                        <p class="text-secondary mb-0"><?= $totalCategorias == 1 ? 'Categoría' : 'Categorías' ?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/facturas')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-receipt fa-3x text-warning mb-3"></i>
                        <h2 class="h4"><?= $totalFacturas ?></h2>
                        <p class="text-secondary mb-0"><?= $totalFacturas == 1 ? 'Factura' : 'Facturas' ?></p>
                    </div>
                </a>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const ctx = document.getElementById('graficoFacturas').getContext('2d');
        const urlEstadisticas = '<?= e(base_path('panel/estadisticas')) ?>';
        let grafico = null;

        function cargarGrafico(periodo) {
            fetch(urlEstadisticas + '?periodo=' + periodo)
                .then(r => r.json())
                .then(datos => {
                    const etiquetas = datos.map(d => d.etiqueta);
                    const totales = datos.map(d => d.total);

                    if (grafico) grafico.destroy();

                    grafico = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: etiquetas,
                            datasets: [{
                                label: 'Total facturado',
                                data: totales,
                                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                                borderColor: 'rgba(13, 110, 253, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    titleFont: { size: 14 },
                                    bodyFont: { size: 14 },
                                    callbacks: {
                                        label: ctx => '$' + ctx.parsed.y.toLocaleString('es-AR', { minimumFractionDigits: 2 })
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 13 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 13 },
                                        callback: v => '$' + v.toLocaleString('es-AR')
                                    }
                                }
                            }
                        }
                    });
                });
        }

        // Filtros
        document.querySelectorAll('[data-periodo]').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('[data-periodo]').forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('btn-outline-light');
                    b.classList.remove('btn-light');
                });
                this.classList.add('active');
                this.classList.remove('btn-outline-light');
                this.classList.add('btn-light');
                cargarGrafico(this.dataset.periodo);
            });
        });

        cargarGrafico('dia');
    </script>

</body>

</html>
