//Quelle: https://learn.sparkfun.com/tutorials/arduino-weather-shield-hookup-guide-v12?_ga=2.174757090.1377162853.1657038170-885100485.1647268005

//Englisch: Beschreibungen von SparkFun
//Deutsch: Anpassungen

//Hinzufügen benötigter Bibliotheken 
#include <Wire.h> 
#include "SparkFunMPL3115A2.h" 
#include "SparkFun_Si7021_Breakout_Library.h" 
#include <ArduinoJson.h>
#include <SoftwareSerial.h>

MPL3115A2 myPressure; //Initialisierung des Drucksensors
Weather myHumidity;//Initialisierung des Feuchtigkeitssensors

SoftwareSerial linkSerial(4, 5); //Aktivieren der SoftwareSerial Bibliothek (Pin 4 = RX, Pin 5 = TX)
//Pin-Definitionen
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// digitale Pins
const byte WSPEED = 3;
const byte RAIN = 2;
const byte STAT1 = 7;
const byte STAT2 = 8;

// analoge Pins
const byte WDIR = A0;

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

//Globale Variablen 
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
long lastWindCheck = 0;
volatile long lastWindIRQ = 0;
volatile byte windClicks = 0;
volatile float lastRainIRQ = 0;
int Calc_time = 300;


//Definition der Variablen für die Speicherung der aufgenommenen Werte
float windgustmph = 0;
float windspdmph_avg = 0; 
int winddir_avg = 0; 
float humidity = 0; 
float temp_h = 0; 
float rainin = 0; 
float pressure = 0;
float temp_avg = 0;
float hum_avg = 0;
float pres_avg = 0;
volatile unsigned long raintime, rainlast, raininterval, rain;

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

//Interruptfunktionen
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

//Regen
void rainIRQ()
{
    raintime = millis(); 
    raininterval = raintime - rainlast; 

    if (raininterval > 20)
    {
        lastRainIRQ += 0.2794; 
        rainlast = raintime; 
    }
}

//Windgeschwindigkeit
void wspeedIRQ()
{
    if (millis() - lastWindIRQ > 10) 
    {
        lastWindIRQ = millis(); 
        windClicks++;
    }
}

// Initialisierung des Skriptes
void setup() {
      Serial.begin(9600); 
    linkSerial.begin(115200);
    Serial.println("Weather Shield Example");

    //setze die Pinbelegung im Code
    pinMode(STAT1, OUTPUT); //Status LED Blau
    pinMode(STAT2, OUTPUT); //Status LED Grün

    pinMode(WSPEED, INPUT_PULLUP); 
    pinMode(RAIN, INPUT_PULLUP); 

    //Initialisiere den Drucksensor
    myPressure.begin(); 
    myPressure.setModeBarometer(); a
    myPressure.setOversampleRate(7); 
    myPressure.enableEventFlags();

    //Initialisiere den Feuchtigkeitssensor
    myHumidity.begin();

    // Verbinde die Interruptfunktionen mit den Hardwarepins
    attachInterrupt(0, rainIRQ, FALLING);
    attachInterrupt(1, wspeedIRQ, FALLING);

    // aktiviere die Interruptfunktionen
    interrupts();

    Serial.println("Weather Shield online!");


}
//Starte die Betriebsschleife
void loop() {


//Definition der temporären Arrays
float windspeed_temp = 0;
long sum = 0; //Winddir.
int D = 0;  //Winddir.
float temp_temp = 0;
float hum_temp = 0;
float pres_temp = 0;

//setze die Geschwindigkeit der Windböen zurück
windgustmph = 0;


// State die Schleife zur Wertmessung
for(int i = 0; i< Calc_time; i++){

// Auslesen der Variablen aus dem Sensor
  
  // Windgeschwindigkeit
  float currentSpeed = get_wind_speed();
  // Windrichutng
  int currentDirection = get_wind_direction();
  // Temperatur
    float temp_h = myHumidity.getTempF();
  // Luftfeuchtigkeit
    float humidity = myHumidity.getRH();
  // Luftdruck
    float pressure = myPressure.readPressure();
    // Reinitialisierung des Drucksensors, falls dieser ausgefallen ist
    if(pressure == -999){
        myPressure.begin(); 
        myPressure.setModeBarometer(); 
        myPressure.setOversampleRate(7); 
        myPressure.enableEventFlags(); 
        float pressure = myPressure.readPressure();
    }
  // Windböen aus der Windgeschwindigkeit errechnen
  if(currentSpeed > windgustmph){windgustmph =currentSpeed;}


//Werte den Arrays hinzufügen
  
  // Temperatur Luftdruck und Luftfeuchtigkeit
  temp_temp += temp_h;
  hum_temp += humidity;
  pres_temp += pressure;

  //Windgeschwindigkeit
  windspeed_temp += currentSpeed;

  //Windrichtung mithilfe der Mitsuta Methode (Quelle:http://abelian.org/vlf/bearings.html)
  int delta = currentDirection - D;

  if(delta < -180)
      D += delta + 360;
  else if(delta > 180)
      D += delta - 360;
  else
      D += delta;
  sum += D;
   

// Abwarten bis eine Sekunde verstrichen ist
  delay(820);
}

//Erstellen eines JSON-Objektes
StaticJsonDocument<192> doc; 

//Berechnen des Minutenmittels der Windgeschwindigkeit | Eintragen in das JSON-Objekt
windspdmph_avg = (windspeed_temp/ Calc_time);
doc["windspeed_avg"]=float(windspdmph_avg)/0.44704;

//Eintragen der stärksten Windböe in das JSON-Objekt
doc["windgust"] =float(windgustmph)/0.44704;

//Berechnung des Minutenmittels der Windrichtung | Eintragen in das JSON-Objekt
winddir_avg = (sum/ Calc_time);
doc["winddir_avg"]=winddir_avg;

//Eintragen des gefallenen Regens in das JSON-Objekt
doc["rain_Sum"]=lastRainIRQ;

//Zurücksetzen der Regenfunktion
lastRainIRQ=0;
raintime = 0;
rainlast = 0;
raininterval = 0;


// Berechnung des Minutenmittels von Temperatur, Druck und Feuchtigkeit | Eintragen in das JSON-Objekt
temp_avg = (temp_temp/Calc_time);
doc["temp_avg"]=float((temp_avg-32)*5/9);

hum_avg = (hum_temp/Calc_time);
doc["hum_avg"]=hum_avg;

pres_avg = (pres_temp/Calc_time);
doc["pres_avg"]=(pres_avg/100);

doc["avg_seconds"]= Calc_time;


//Versenden des JSON-Objekts mittels der erstellten seriellen Schnittstelle 
serializeJson(doc,linkSerial);

}


//Funktion für die Bestimmung der aktuellen Windgeschwindigkeit anhand der Interruptfunktion
float get_wind_speed()
{
    float deltaTime = millis() - lastWindCheck; //750ms

    deltaTime /= 1000.0; //Konvertierung in Sekunden

    float windSpeed = (float)windClicks / deltaTime; //Bestimmung der Geschwindigkeit

    windClicks = 0; //Werte der Interruptfunktion zurücksetzen 
    lastWindCheck = millis();

    windSpeed *= 1.492; //4 * 1.492 = 5.968MPH

    return(windSpeed);
}

//Funktion zur Bestimmung der Windrichtung 
int get_wind_direction()
{
    unsigned int adc;

    adc = analogRead(WDIR); //Beziehe den Aktuellen Widerstand des Sensors 

    
    //Bestimme anhand des Widerstandswert den Winkel 
    if (adc < 380) return (113);
    if (adc < 393) return (68);
    if (adc < 414) return (90);
    if (adc < 456) return (158);
    if (adc < 508) return (135);
    if (adc < 551) return (203);
    if (adc < 615) return (180);
    if (adc < 680) return (23);
    if (adc < 746) return (45);
    if (adc < 801) return (248);
    if (adc < 833) return (225);
    if (adc < 878) return (338);
    if (adc < 913) return (0);
    if (adc < 940) return (293);
    if (adc < 967) return (315);
    if (adc < 990) return (270);
    return (-1); // error, disconnected?
}
