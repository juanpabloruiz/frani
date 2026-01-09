<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frani</title>

    <!-- Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <main class="container py-5">

        <?php
        include('pdo.php');

        $id = $_GET['edit'] ?? null;
        $editData = null;

        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            $editData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>

        <?php if ($editData): ?>

            <!-- Formulario edición -->
            <form method="POST" action="update" class="d-flex gap-2 mb-3">
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                <input type="text" name="code" class="form-control" value="<?= $editData['code'] ?>">
                <input type="text" name="title" class="form-control" value="<?= $editData['title'] ?>">
                <input type="text" name="price" class="form-control" value="<?= $editData['price'] ?>">
                <input type="submit" value="Actualizar" class="btn btn-primary">
            </form>

        <?php else: ?>

            <!-- Formulario inserción -->
            <form method="POST" action="/insert" class="d-flex gap-2 mb-3">
                <input type="text" name="code" class="form-control" placeholder="Código de barras">
                <input type="text" name="title" class="form-control" placeholder="Producto">
                <input type="text" name="price" class="form-control" placeholder="Precio">
                <input type="submit" value="Agregar" class="btn btn-primary">
            </form>

        <?php endif; ?>

        <!-- Tabla de productos -->
        <table class="table">
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Editar</th>
            </tr>

            <?php
            $stmt = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
            while ($p = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
                <tr>
                    <td><?= $p['code'] ?></td>
                    <td><?= $p['title'] ?></td>
                    <td>$ <?= $p['price'] ?></td>
                    <td><a href="?edit=<?= $p['id'] ?>">Editar</a></td>
                </tr>
            <?php endwhile; ?>
        </table>

    </main>

</body>

</html>