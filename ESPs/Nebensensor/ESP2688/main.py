#importiere Bibliotheken
import machine
import time
import BME280
import network
import mqtt
import ubinascii
import ntptime
import json
import network
import wifi

#definiere die Verbindung zum MQTT-Brocker
mqtt_server = '141.64.196.177'

#definiere die Hauptschleife die sich während des Betriebes dauerhaft wiederholt
while True:
  #Abfrage der Werte des BME280 Quelle: https://randomnerdtutorials.com/micropython-bme280-esp32-esp8266/
  try:
    i2c = machine.I2C(scl=machine.Pin(5), sda=machine.Pin(4), freq=10000)
    bme = BME280.BME280(i2c=i2c)
    temp = bme.temperature
    hum = bme.humidity
    pres = bme.pressure
    print("BME280 Connection")
  except:
    print("BME280 Error")

  #Versuche die Verbindung zum W-LAN herzustellen. Wenn diese bereits verbunden ist passiert nichts.
  try:
    wifi.connect_wifi()
    time.sleep(2)
    #Zeitabgleich
    ntptime.host ="1.europe.pool.ntp.org"
    ntptime.settime()
  except:  
    print("Time Sync error")

  try:
    #aktualisiere die Systemzeit
    data_time = time.localtime()
    #erstelle ein JSON-Objekt mit den ausgelesenen Daten und fügen einen Zeitstempel hinzu
    pub ={"Temp": temp,"Hum": hum, "Pres": pres, "Time":"%s/%s/%s %s:%s:%s"%(data_time[0],data_time[1],data_time[2],data_time[3],data_time[4],data_time[5])}
    pub = json.dumps(pub)
    print(pub)
  except:
    print("Json error")
  
  try:
    #nochmalige Überprüfung der W-LAN-Verbindung
    wifi.connect_wifi()
    time.sleep(2)
    #generieren einer Client-ID für den MQTT-Brocker
    client_id = ubinascii.hexlify(machine.unique_id())
    #Definition des MQTT-Client-Objektes
    client=mqtt.MQTTClient(client_id,mqtt_server,user='BHT-Wetter',password='cUMRHkKhw66AQwNMmPPt', keepalive=120)
    client.connect()
    time.sleep(2)
    #Senden des JSON-Objektes an den MQTT-Brocker
    client.publish(b"Validation",b"%s"%data,qos=0)
    time.sleep(2)
    client.disconnect()
    print("Daten gesendet")
  except:
    print("Network error") 
    
  time.sleep(297)
  
  
  
 

