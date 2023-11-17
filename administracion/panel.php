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
    <title>Panel</title>
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
                    <li class="nav-item"><a class="nav-link" href="../status">Estadísticas</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="bg-primary p-4">
        <a href="./"><img src="../img/logo.png" alt="Frani" class="img-fluid d-block mx-auto"></a>
    </header>
    <main class="container my-4">
        <div class="row">
            <div class="col-md-4">
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE id = '$id'");
                    $campo = mysqli_fetch_array($consulta);
                ?>
                    <form method="post" action="modificacion_panel" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $campo['id']; ?>">
                        <div class="mb-3"><input type="text" name="producto" value="<?php echo $campo['producto']; ?>" class="form-control"></div>
                        <div class="mb-3 input-group"><span class="input-group-text">$</span><input type="number" name="precio" value="<?php echo $campo['precio']; ?>" class="form-control"></div>
                        <div class="mb-3 input-group"><span class="input-group-text">$</span><input type="number" name="costo" value="<?php echo $campo['costo']; ?>" class="form-control"></div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="estado" role="switch" id="flexSwitchCheckDefault" <?php echo ($campo['estado'] == 'publico') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Visible en tienda</label>
                        </div>
                        <h5>Seleccionar categoría</h5>
                        <select name="categoria" class="form-select mb-3">
                            <option value="<?php echo $campo['categoria']; ?>"><?php echo $campo['categoria']; ?></option>    
                            <option value="Otros">Otros</option>
                            <option value="Librería">Librería</option>
                            <option value="Sublimación">Sublimación</option>
                        </select>
                        <div class="mb-3"><input type="file" name="foto" class="form-control"></div>
                        <div class="d-grid mb-3"><input type="submit" name="actualizar" value="Modificar" class="btn btn-primary"></div>
                    </form>
                <?php
                } else {
                ?>
                    <form method="post" action="alta_panel" enctype="multipart/form-data">
                        <div class="mb-3"><input type="text" name="producto" placeholder="Producto" class="form-control"></div>
                        <div class="mb-3 input-group"><span class="input-group-text">$</span><input type="number" name="precio" placeholder="Precio" class="form-control"></div>
                        <div class="mb-3 input-group"><span class="input-group-text">$</span><input type="number" name="costo" placeholder="Costo" class="form-control"></div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="estado" role="switch" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Visible en tienda</label>
                        </div>
                        <h5>Seleccionar categoría</h5>
                        <select name="categoria" class="form-select mb-3">
                            <option value="Otros">Otros</option>
                            <option value="Librería">Librería</option>
                            <option value="Sublimación">Sublimación</option>
                        </select>
                        <div class="mb-3"><input type="file" name="foto" class="form-control"></div>
                        <div class="d-grid mb-3"><input type="submit" name="insertar" value="Agregar" class="btn btn-primary"></div>
                    </form>
                <?php
                }
                ?>
            </div>
            <div class="col-md-8">
                <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar" class="form-control mb-3">
                <hr>
                    <div>
                        <h5>Generar interés global sobre los productos</h5>
                        <form method="post" action="porcentaje">
                            <?php
                            if (isset($_POST['porcentual'])) {
                                $categoria = $_POST['categoria'];
                                $porcentaje = $_POST['porcentaje'];
                                $porcentaje_entero = round($porcentaje);
                                $consulta = "UPDATE productos SET precio = ROUND(precio * (1 + ?/100)) WHERE categoria = '$categoria'";
                                $sentencia = $conexion->prepare($consulta);
                                $sentencia->bind_param("i", $porcentaje_entero);
                                if ($sentencia->execute()) {
                                    echo '<script>window.location="panel"</script>';
                                } else {
                                    echo '<div class="alert alert-danger">Error en la actualización: ' . $sentencia->error . '</div>';
                                }
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-auto">
                                    <select name="categoria" class="form-select mb-3">
                                        <option value="otros">Otros</option>
                                        <option value="libreria">Librería</option>
                                        <option value="sublimacion">Sublimación</option>
                                    </select>
                                </div>
                                <div class="col-md-auto">
                                    <div class="input-group mb-3">
                                        <input type="number" name="porcentaje" class="form-control" aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text">%</span>
                                        <input type="submit" name="porcentual" value="Aplicar aumento" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr>
                <div id="datos">
                    
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Costo</th>
                                <th>Borrar</th>
                            </tr>
                            <?php
                            $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
                            while ($campo = mysqli_fetch_array($consulta)) {
                                $producto = $campo['producto'];
                            ?>
                                <tr <?php echo ($campo['estado'] == 'publico') ? 'class="bg-success text-white"' : 'class="bg-secondary text-white"'; ?>>
                                    <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['producto']; ?></td>
                                    <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['precio']; ?></td>
                                    <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['costo']; ?></td>
                                    <td><a href="baja_panel?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea borrar <?php echo $producto; ?>?')" class="btn btn-danger"><i class="bi bi-trash3"></i></a></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-dark text-white text-center py-4">
        Frani <?php echo date('Y'); ?> - <a href="salir">Cerrar sesión</a>
    </footer>
    <script src="../js/buscar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>