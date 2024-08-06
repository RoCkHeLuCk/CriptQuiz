#include "main.hpp"

void setup()
{
   Serial.begin(9600);
   pinMode(LED_BUILTIN, OUTPUT);
   digitalWrite(LED_BUILTIN, LOW);
   pinMode(A0, ANALOG);
}

char command = '\0';
uint16_t temp = 0;

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

      temp = analogRead(A0);
      Serial.printf("%05.2f\n",temp);
   }
}
