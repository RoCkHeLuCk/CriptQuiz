#include <Arduino.h>

// debug
#ifdef DEBUG
   #define DEBUG_ESP_PORT Serial
   #define DEBUG_ESP_BAUD 9600 // bps
   #ifdef DEBUG_ESP_PORT
      #define DEBUG_PRINT(fmt, ...) DEBUG_ESP_PORT.printf_P((PGM_P)PSTR(fmt), ##__VA_ARGS__)
   #endif
#else
   #define DEBUG_PRINT(...) do { (void)0; } while (0)
#endif