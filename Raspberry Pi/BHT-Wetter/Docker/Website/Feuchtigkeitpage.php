<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHT-Wetter</title>
    <link rel="stylesheet" href="style.css">

    <!--Definition der CDN-Links für das Einbungen der JavaScript-Erweiterungen -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/10.6.1/math.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.27.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@0.1.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/1.2.1/chartjs-plugin-zoom.js"></script>
</head>
<body>
<!--beginn der Visualisierung der Datenseiten-->      
<div class="data_container">
    <h2>Luftfeuchtigkeit</h2>

    <div class="chart">
        <!--Knopfelement für das Zurücksetzen der Diagrammansicht-->
        <button id="reset_zoom">Ansicht zurücksetzen</button>

        <!--HTML-Canvas-Objekt für das Einbinden der Chart.js-Diagramme-->
        <canvas id="Hum_Chart" ></canvas>
        <p>Feuchtigkeitsmittelwerte aufgenommen in einem 5 min Intervall </p>

        <!--Verlinkung der für das Diagramm genutzten Daten-->
        <P><a href="/PHP/Feuchtigkeit.php"> ->Daten abrufen</a></P>
    </div>

    <!--Einbinden des JavaScript-Skriptes für die Darstellung der Diagramme-->
    <script src="JavaScript/Hum-chart.js"></script>
        
</div>

</body>
</html>

