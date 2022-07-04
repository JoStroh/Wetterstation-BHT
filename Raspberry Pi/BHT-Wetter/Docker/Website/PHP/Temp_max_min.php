<?php
//Quelle: https://dyclassroom.com/chartjs/chartjs-how-to-create-line-graph-using-data-from-mysql-mariadb-table-and-php

//definiere Datenbankeinträge
define('DB_HOST', 'mariaDB:3306');
define('DB_USERNAME', 'Admin');
define('DB_PASSWORD', 'WCYQ94m95SLEnGLwm7Fz');
define('DB_NAME', 'BHT-Wetter');

//verbindet sich mit der Datenbank
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

//Maximaltemperatur

//definiere SQL-Abfrage
//es wird die Maximaltemperatur des aktuellen Tages abgefragt
$query_max = sprintf("SELECT Temp AS Temp, cast(StatusTime as Time) as Time FROM Temp_Hum_Pres WHERE Temp =(SELECT MAX(Temp) FROM Temp_Hum_Pres WHERE Cast(StatusTime as Date) = CURRENT_DATE) ORDER BY StatusTime desc LIMIT 1 ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_max = $mysqli->query($query_max);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data_max = array();
foreach ($result_max as $row) {
  $data_max[] = $row;
}

//Minimaltemperatur

//definiere SQL-Abfrage
//es wird die Minimaltemperatur des aktuellen Tages abgefragt
$query_min = sprintf("SELECT Temp AS Temp, cast(StatusTime as Time) as Time FROM Temp_Hum_Pres WHERE Temp =(SELECT MIN(Temp) FROM Temp_Hum_Pres WHERE Cast(StatusTime as Date) = CURRENT_DATE) ORDER BY StatusTime desc LIMIT 1 ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_min = $mysqli->query($query_min);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data_min = array();
foreach ($result_min as $row) {
  $data_min[] = $row;
}

// Durschnittstemperatur

//definiere SQL-Abfrage
//es wird der Mittelwert des aktuellen Tages abgefragt
$query_mean = sprintf("SELECT AVG(Temp) AS Temp FROM Temp_Hum_Pres WHERE Cast(Temp_Hum_Pres.StatusTime as Date) = CURRENT_DATE ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_mean = $mysqli->query($query_mean);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data_mean = array();
foreach ($result_mean as $row) {
  $data_mean[] = $row;
}


//schließe die Abfrageergebnisse
$result_max->close();
$result_min->close();
$result_mean->close();

//schließe die Verbindung zur Datenbank
$mysqli->close();

//formatiere die Zeitstempel
$max_time = explode(":",$data_max[0]['Time']);
$min_time = explode(":",$data_min[0]['Time']);
$data_mean = number_format($data_mean[0]['Temp'],2);

//schreibe die Ergebnisse in einem Text auf die PHP-Seite
echo"Maximaltemperatur: {$data_max[0]['Temp']} °C um {$max_time[0]}:{$max_time[1]} Uhr<br>";
echo "Minimaltemperatur:  {$data_min[0]['Temp']} °C um {$min_time[0]}:{$min_time[1]} Uhr<br>";
echo "aktuelle Durchschnitstemperatur: {$data_mean} °C";
?>