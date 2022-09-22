<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Frani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="panel">Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas">Ventas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="bg-primary p-4">
        <a href="./"><img src="../img/logo.png" alt="Frani" class="img-fluid d-block mx-auto"></a>
    </header>
    <main class="container my-4">
        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $consulta = mysqli_query($conexion, "SELECT * FROM ventas WHERE id = '$id'");
            $campo = mysqli_fetch_array($consulta);
        ?>
        <h4>Modificar la venta</h4>
        <div class="card">
            <div class="card-body">
                <form method="post" action="modificacion_venta">
                    <input type="hidden" name="id" value="<?php echo $campo['id']; ?>">
                    <div class="row">
                        <div class="col">
                            <select name="producto" class="form-control">
                                <option value="<?php echo $campo['producto']; ?>"><?php echo $campo['producto']; ?></option>
                            </select>
                        </div>
                        <div class="col"><input type="number" name="cantidad" value="<?php echo $campo['cantidad']; ?>" class="form-control"></div>
                        <div class="col d-grid"><input type="submit" name="venta" value="Modificar venta" class="btn btn-primary"></div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        } else {
        ?>
        <h4>Agregar una nueva venta</h4>
        <div class="card">
            <div class="card-body">
                <form method="post" action="alta_venta">
                    <div class="row">
                        <div class="col">
                            <select name="producto" class="form-control">
                                <?php
                                $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
                                while ($campo = mysqli_fetch_array($consulta)) {
                                ?>
                                    <option value="<?php echo $campo['producto']; ?>"><?php echo $campo['producto']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col"><input type="number" name="cantidad" placeholder="Cantidad" class="form-control"></div>
                        <div class="col d-grid"><input type="submit" name="venta" value="Agregar venta" class="btn btn-primary"></div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        }
        ?>
        <h1>Ventas</h1>
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unitario</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Borrar</th>
                </tr>
            </thead>
            <?php
            $consulta = mysqli_query($conexion, "SELECT * FROM ventas ORDER BY id DESC");
            while ($campo = mysqli_fetch_array($consulta)) {
                $producto = $campo['producto'];
            ?>
                <tbody>
                    <tr>
                        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['producto']; ?></td>
                        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['cantidad']; ?></td>
                        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'">$ <?php echo $campo['unitario']; ?></td>
                        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'">$ <?php echo $campo['total']; ?></td>
                        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['registro']; ?></td>
                        <td><a href="baja_venta?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea borrar <?php echo $producto; ?>?')" class="btn btn-danger"><i class="bi bi-trash3"></i></a></td>
                    </tr>
                </tbody>
            <?php
            }
            ?>
        </table>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>