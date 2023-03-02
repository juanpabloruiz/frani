<?php
include('conexion.php');
if (isset($_SESSION['correo'])) {
    echo '<script>window.location="panel"</script>';
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body>
    <header class="bg-primary p-4">
        <a href="../"><img src="../img/logo.png" alt="Frani" class="img-fluid d-block mx-auto"></a>
    </header>
    <main class="container my-4">
        <div class="row justify-content-md-center">
            <div class="col-md-4">
                <form method="post" action="ingreso">
                    <div class="mb-3"><input type="email" name="correo" class="form-control" placeholder="Correo electrónico"></div>
                    <div class="mb-3"><input type="password" name="clave" class="form-control" placeholder="Contraseña"></div>
                    <div class="d-grid mb-3"><input type="submit" name="ingreso" value="Ingresar" class="btn btn-primary"></div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>