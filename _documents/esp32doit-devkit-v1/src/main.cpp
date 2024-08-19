#include "main.hpp"
#define PIN_CONTROLLER GPIO_NUM_13
void setup()
{
   Serial.begin(9600);
   pinMode(PIN_CONTROLLER, OUTPUT);
   digitalWrite(PIN_CONTROLLER, LOW);
}

char command = '\0';
void loop()
{

   if (Serial.available() > 0)
   {
      command = Serial.read();
      switch (command)
      {
         case 'd': digitalWrite(PIN_CONTROLLER, HIGH); break;
         case 'l': digitalWrite(PIN_CONTROLLER, LOW); break;
      }
   }
}
