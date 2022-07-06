#importiere Bibliotheken
import time
import machine
import uos
import ubinascii
import mqtt
import ntptime
import json
import network
import wifi

#definiere die Verbindung zum MQTT-Brocker
mqtt_server = '141.64.196.177'


#definiere die Hauptschleife die sich während des Betriebes dauerhaft wiederholt
while True:
    #UART 2 initialisieren
    uart = machine.UART(2, baudrate=115200,rxbuf=500)
    uart.init(115200, bits=8, parity=None, stop=1)
    
    #UART 2 auslesen
    data = str(uart.readline())

    
    #definiere eine Schleife die so lange die UART-Schnittstelle abfragt, bis diese das JSON-Objekt des Arduino enthält
    while str(data)[-2] !="}" and str(data)[2] !="{" and len(str(data)) < 100: 
      time.sleep(0.5)
      data = str(uart.readline())
      
    
    #Versuche die Verbindung zum W-LAN herzustellen. Wenn diese bereits verbunden ist passiert nichts.
    try:
      wifi.connect_wifi()
      time.sleep(2)
      #Zeitabgleich
      ntptime.host ="1.europe.pool.ntp.org"
      ntptime.settime()
      
    except:  
      print("Time Sync error")
    
    #lade das JSON-Objekt und versehe es mit einem Zeitstempel 
    try:
      data = json.loads(str(data)[2:-1])
      data_time = time.localtime()
      data['UTC_timestamp']=(("%s/%s/%s %s:%s:%s")%(data_time[0],data_time[1],data_time[2],data_time[3],data_time[4],data_time[5]))
      data=json.dumps(data)
      print(data)
    except:
      print("JSON Error")
        
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
      client.publish(b"Arduino",b"%s"%data,qos=0)
      time.sleep(2)
      client.disconnect()
      print("Daten gesendet")
    except:
      print("Network error") 
    
    time.sleep(3)
      









