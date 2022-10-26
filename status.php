<?php
session_start();
$db = new mysqli('localhost', 'soledad', 'Rufus/80', 'soledad_db');
mysqli_set_charset($db, 'utf8');
$result = $db->query("SELECT registro,total FROM ventas");
?>
 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['reg', 'tot'],
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "['" . $row['registro'] . "', " . $row['total'] . "],";
                    }
                }
                ?>
            ]);

            var options = {
                legend: 'none',
                width: '100%',
                height: '350',
                chartArea: {
                    left: 2,
                    right: 2,
                    bottom: 20,
                    top: 0,
                    width: "100%",
                    height: "100%"
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('data'));

            chart.draw(data, options);

        }
    </script>
    
   
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
        <a href="./"><img src="data:image/png;base64, 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" alt="Frani" class="img-fluid d-block mx-auto"></a>
    </header>
    <main class="container my-4">
        
        <?php
        $query = mysqli_query($db, "SELECT SUM(total) AS total, DATE_FORMAT(registro, '%d/%m') AS fecha FROM `ventas` GROUP BY fecha ORDER BY fecha DESC");
        while($data = mysqli_fetch_array($query)) {
            echo $data['fecha'].' = $ '.$data['total'].'<br>';
            
            //$date=date_create($registro);
            //echo date_format($date,"Y/m");
        }
        ?>
    <div id="data"></div>
    </main>
    </body>
    </html>