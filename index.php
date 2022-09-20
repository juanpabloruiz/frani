<?php
include('administracion/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    if (isset($_SESSION['correo'])) {
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="administracion/panel">Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="administracion/ventas">Ventas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
    }
    ?>
    <header class="bg-primary p-4">
        <a href="./"><img src="img/logo.png" alt="Frani" class="img-fluid d-block mx-auto"></a>
    </header>
    <main class="container my-4">
        <div class="row">
            <?php
            $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE estado = 'publico' ORDER BY id DESC");
            while ($campo = mysqli_fetch_array($consulta)) {
            ?>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <img src="imagenes/<?php echo $campo['foto']; ?>" class="card-img-top img-fluid" alt="<?php echo $campo['producto']; ?>">
                        <div class="card-body">
                            <h5 class="card-title">$ <?php echo $campo['precio']; ?></h5>
                            <p class="card-text"><?php echo $campo['producto']; ?></p>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </main>
    <footer class="bg-dark text-white text-center py-4">
        Frani <?php echo date('Y'); ?> -
        <?php
        if (isset($_SESSION['correo'])) {
        ?>
            <a href="administracion/salir">Cerrar sesión</a>
        <?php
        } else {
        ?>
            <a href="administracion">Iniciar sesión</a>
        <?php
        }
        ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>