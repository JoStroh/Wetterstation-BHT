from MQTT_Subscribe import subscribe # angepasste Paho-Funktion 
import json
import mysql.connector
import datetime
from dateutil import tz
import pytz

#definiere die MQTT-Anmeldedaten
host ="mqtt"
user ='BHT-Wetter'
pw = 'PASSWORD'

#MQTT-Verbindung herstellen und eine Topic abfragen
try:
  pay = subscribe(topic="Arduino",hostname= host, username = user, password = pw,timeout= 300).payload
  print(pay)
except:
  print('Message empty')

#Daten aus dem empfangen JSON-Objekt lagen
pay_ob = json.loads(pay)

#Variablen formatieren
temp = "{:.2f}".format(pay_ob["temp_avg"])
pres = "{:.2f}".format(pay_ob["pres_avg"])
hum = "{:.2f}".format(pay_ob["hum_avg"])

#UTC Zeit in Localtime umwandeln
date_time_utc = datetime.datetime.strptime(str(pay_ob["UTC_timestamp"]),"%Y/%m/%d %H:%M:%S").replace(tzinfo=pytz.UTC)
date_time =date_time_utc.astimezone(tz.tzlocal())

#an der Datenbank anmelden
mydb = mysql.connector.connect(
  host="mariaDB",
  user="Admin",
  password="WCYQ94m95SLEnGLwm7Fz",
  database="BHT-Wetter"
)
#Daten in die Datenbank schreiben  
mycursor = mydb.cursor()
sql = "INSERT INTO Temp_Hum_Pres (Temp,Hum,Pres,StatusTime) VALUES (%s,%s,%s,%s)"
val = (str(temp),str(hum),str(pres),date_time)

#die geschriebenen Daten bestätigen
try:
  mycursor.execute(sql, val)
  mydb.commit()
except mysql.connector.Error as err:
  print("Something went wrong: {}".format(err))