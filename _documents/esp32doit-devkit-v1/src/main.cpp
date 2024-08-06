#include "main.hpp"

void setup()
{
   Serial.begin(9600);
   pinMode(LED_BUILTIN, OUTPUT);
   digitalWrite(LED_BUILTIN, LOW);
}

char command = '\0';
void loop()
{

   if (Serial.available() > 0)
   {
      command = Serial.read();
      switch (command)
      {
         case 'd': digitalWrite(LED_BUILTIN, LOW); break;
         case 'l': digitalWrite(LED_BUILTIN, HIGH); break;
      }
   }
}
