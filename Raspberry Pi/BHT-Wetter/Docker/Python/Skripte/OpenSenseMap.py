from MQTT_Subscribe import subscribe # angepasste Paho-Funktion 
import json
import requests


#definiere die Anmeldedaten für den HTTP-Request
headers = {'content-type': 'application/json'}
url = 'https://api.opensensemap.org/boxes/629b506c87a60b001cd2bb30/data'


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
Rain = "{:.3f}".format(pay_ob["rain_Sum"])
windspeed = "{:.2f}".format(pay_ob["windspeed_avg"])

#Daten in ein neues JSON-Objekt laden
pay = {
    "629b506c87a60b001cd2bb35":temp,
    "629b506c87a60b001cd2bb34":pres,
    "629b506c87a60b001cd2bb33":hum,
    "629b506c87a60b001cd2bb32":Rain,
    "629b506c87a60b001cd2bb31":windspeed
}
pay =json.dumps(pay)

# Daten an den Server Senden
r= requests.post(url,data=pay, headers=headers)