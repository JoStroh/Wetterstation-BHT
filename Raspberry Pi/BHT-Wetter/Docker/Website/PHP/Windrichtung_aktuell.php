<?php


//definiere Datenbankeinträge
// ist in diesem Fall nicht nötig, da auf der Seite der Website diese schon in einem anderen Skript definiert wurden
//define('DB_HOST', 'mariaDB:3306');
//define('DB_USERNAME', 'Admin');
//define('DB_PASSWORD', 'WCYQ94m95SLEnGLwm7Fz');
//define('DB_NAME', 'BHT-Wetter');

//verbindet sich mit der Datenbank
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

//definiere SQL-Abfrage
$query = sprintf("SELECT Winddir, StatusTime FROM Wind  ORDER BY StatusTime desc LIMIT 1");

//ausführen der SQL-Abfrage in der verbundenen Datenbank
$result = $mysqli->query($query);

//Schleife über die Abfrageergebnisse, die die Daten in ein besser verwertbares Array schreibt
$data = array();
foreach ($result as $row) {
  $data[] = $row;
  
}

//schließe die Abfrageergebnisse
$result->close();

//schließe die Verbindung zur Datenbank
$mysqli->close();


//überführe die gegebenen Winkel in Himmelsrichtungen
$Windrichtung = '';

if(intval($data[0]['Winddir'])>302 and intval($data[0]['Winddir']) <348 ){
  $Windrichtung = 'Nordost';
}
elseif(intval($data[0]['Winddir'])>258 and intval($data[0]['Winddir']) <302 ){
  $Windrichtung = 'Nord';
}
elseif(intval($data[0]['Winddir'])>213 and intval($data[0]['Winddir']) <258 ){
  $Windrichtung = 'Nordwest';
}
elseif(intval($data[0]['Winddir'])>168 and intval($data[0]['Winddir']) <213 ){
  $Windrichtung = 'West';
}
elseif(intval($data[0]['Winddir'])>123 and intval($data[0]['Winddir']) <168 ){
  $Windrichtung = 'Südwest';
}
elseif(intval($data[0]['Winddir'])>78 and intval($data[0]['Winddir']) <123 ){
  $Windrichtung = 'Süd';
}
elseif(intval($data[0]['Winddir'])>33 or intval($data[0]['Winddir']) <78 ){
  $Windrichtung = 'Südost';
}
elseif(intval($data[0]['Winddir'])>348 or intval($data[0]['Winddir']) <33 ){
  $Windrichtung = 'Ost';
}

//schreibe die Ergebnisse in einem Text auf die PHP-Seite
echo "Aktuelle Windrichutung: {$Windrichtung}";
?>