#Quelle: https://docs.micropython.org/en/latest/esp8266/tutorial/network_basics.html

def connect_wifi():
    import network

    # Anmeldedaten
    ssid = 'Fritzi_SwissMiss'
    password = '78385400378837214860'
    
    #access point deaktivieren
    acc= network.WLAN(network.AP_IF)
    acc.active(False)
    
    #connect to Network
    station = network.WLAN(network.STA_IF)
    station.active(True)
    station.connect(ssid,password)
    
    while station.isconnected() == False:
            pass
            
    print('Connection successful')        
    print(station.ifconfig())
