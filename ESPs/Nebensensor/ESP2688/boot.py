#importiere Bibliotheken
import ntptime
import time
import wifi

#rufe die Funktion für die W-LAN-Verbindung auf
wifi.connect_wifi()
time.sleep(2)

#versuche die Systemzeit mittels eines Servers zu synchronisieren 
try:
  ntptime.host ="1.europe.pool.ntp.org"
  ntptime.settime()
except:
  print("Time Sync error")