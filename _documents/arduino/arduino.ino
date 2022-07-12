void setup() 
{
  Serial.begin(9600);
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, LOW);
}

char command = '\0';
// the loop routine runs over and over again forever:
void loop() 
{
   
   if (Serial.available() > 0) 
   {
   
    command = Serial.read();
    if (command == 'd')
    {
      digitalWrite(LED_BUILTIN, HIGH);
    }
    if (command == 'l')
    {
      digitalWrite(LED_BUILTIN, LOW);
    }
    Serial.write(command);
  }
}
