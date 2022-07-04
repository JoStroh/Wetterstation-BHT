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

//Abfrage der Regensumme

//definiere SQL-Abfrage
// es werden nur die Werte des aktuellen Tages abgefragt
$query_ = sprintf("SELECT Rain  FROM Rain WHERE Cast(StatusTime as Date) = CURRENT_DATE ");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_ = $mysqli->query($query_);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
//die abgefragten Werte werden aufsummiert
$data = 0;
foreach ($result_ as $row) {
  $data = $data +$row["Rain"];
}

// Abfrage des letzten Regenevents

//definiere SQL-Abfrage
// es wird das letzte Regenevent abgefragt
$query_last = sprintf("SELECT StatusTime as Time FROM Rain WHERE Rain.Rain > 0 ORDER BY StatusTime DESC LIMIT 1;");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result_last = $mysqli->query($query_last);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
// hierbei gibt es nur einen Durchgang, da die Abfrage nur ein Event enthält
$data_last = array();
foreach ($result_last as $row) {
  $data_last[] = $row;
}


//schließe die Abfrageergebnisse
$result_->close();
$result_last->close();


//schließe die Verbindung zur Datenbank
$mysqli->close();

//formatiere den Zeitstempel der Abfrage nach dem letzten Regenevent
$date_last = explode("-",explode(" ",$data_last[0]['Time'])[0]);
$time_last = explode(":",explode(" ",$data_last[0]['Time'])[1]);


//schreibe die Ergebnisse in einem Text auf die PHP-Seite
echo"heutiger Niederschlag: {$data} Liter<br>";
echo"letztes Regenevent: {$date_last[2]}.{$date_last[1]}.{$date_last[0]} um {$time_last[0]}:{$time_last[1]} Uhr";
?>