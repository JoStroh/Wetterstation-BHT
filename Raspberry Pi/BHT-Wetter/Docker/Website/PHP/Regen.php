<?php
//Quelle: https://dyclassroom.com/chartjs/chartjs-how-to-create-line-graph-using-data-from-mysql-mariadb-table-and-php

//definiere den Typ der PHP-Seite
header('Content-Type: application/json');

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

//definiere SQL-Abfrage
$query = sprintf("SELECT Rain, StatusTime FROM Rain  WHERE   Rain.StatusTime > DATE_SUB(NOW(), INTERVAL 48 HOUR)");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result = $mysqli->query($query);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
//wenn ein Regenevent Stadtfindet werden alle folgenden Events auch addiert, bis kein Regen mehr fällt
$data = array();
$rain_i = 0;
foreach ($result as $row) {
  if(floatval($row['Rain'])>0){
    $rain_i = $rain_i +floatval($row['Rain']);
    array_push($data, array('Rain' =>$rain_i ,'StatusTime' =>$row['StatusTime'])); 
  }
  else{
    $data[] = $row;
    $rain_i = 0;
  }
  
}

//schließe die Abfrageergebnisse
$result->close();

//schließe die Verbindung zur Datenbank
$mysqli->close();

//schreibe das neue Array als JSON-Objekt auf die PHP-Seite
print json_encode($data);
