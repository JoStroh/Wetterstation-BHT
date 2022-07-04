<?php
//Quelle: https://dyclassroom.com/chartjs/chartjs-how-to-create-line-graph-using-data-from-mysql-mariadb-table-and-php

//definiere den Typ der PHP-Seite
define('DB_HOST', 'mariaDB:3306');
define('DB_USERNAME', 'Admin');
define('DB_PASSWORD', 'WCYQ94m95SLEnGLwm7Fz');
define('DB_NAME', 'BHT-Wetter');

//definiere Datenbankeinträge
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

//Maximale Böe

//definiere SQL-Abfrage
//es wird die maximale Böengeschwindigkeit des Tages abgefragt
$query_max = sprintf("SELECT Windgust AS Windgust, cast(StatusTime as Time) as Time FROM Wind WHERE Windgust =(SELECT MAX(Windgust) FROM Wind WHERE Cast(StatusTime as Date) = CURRENT_DATE) ORDER BY StatusTime desc LIMIT 1 ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_max = $mysqli->query($query_max);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data_max = array();
foreach ($result_max as $row) {
  $data_max[] = $row;
}

//definiere SQL-Abfrage
//es wird dier Mittelwert des Tages abgefragt
$query_mean = sprintf("SELECT AVG(Windgust) AS Windgust FROM Wind WHERE Cast(Wind.StatusTime as Date) = CURRENT_DATE ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_mean = $mysqli->query($query_mean);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data_mean = array();
foreach ($result_mean as $row) {
  $data_mean[] = $row;
}

//schließe die Abfrageergebnisse
$result_max->close();
$result_mean->close();

//schließe die Verbindung zur Datenbank
$mysqli->close();

//formatiere die Zeitstempel
$max_time = explode(":",$data_max[0]['Time']);
$data_mean = number_format($data_mean[0]['Windgust'],2);

//schreibe die Ergebnisse in einem Text auf die PHP-Seite
echo"Stärkste Böe: {$data_max[0]['Windgust']} km/h um {$max_time[0]}:{$max_time[1]} Uhr<br>";
echo "durchschnittliche Böengeschwindigkeit: {$data_mean} km/h";
?>