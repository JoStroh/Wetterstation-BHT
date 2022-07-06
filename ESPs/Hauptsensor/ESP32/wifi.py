#Quelle: https://docs.micropython.org/en/latest/esp8266/tutorial/network_basics.html

def connect_wifi():
    import network
    
    
    # Anmeldedaten
    ssid = 'Fritzi_SwissMiss'
    password = '78385400378837214860'
    
    sta_if = network.WLAN(network.STA_IF)
    if not sta_if.isconnected():
        print('connecting to network...')
        sta_if.active(True)
        sta_if.connect(ssid,password)
        while not sta_if.isconnected():
            pass
    print('network config:', sta_if.ifconfig())




