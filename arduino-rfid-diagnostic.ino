#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 10
#define RST_PIN 9

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance

void setup() {
  Serial.begin(9600);   // Initialize serial communication
  while (!Serial);       // Wait for serial port to connect (for some boards)
  
  SPI.begin();          // Initialize SPI bus
  mfrc522.PCD_Init();    // Initialize MFRC522
  
  // Wait a moment for initialization
  delay(100);
  
  // DIAGNOSTIC: Check if RC522 is connected
  Serial.println("=== RC522 DIAGNOSTIC ===");
  
  // Read version register
  byte version = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  Serial.print("RC522 Version: 0x");
  Serial.println(version, HEX);
  
  if (version == 0x00 || version == 0xFF) {
    Serial.println("ERROR: RC522 not detected! Check wiring:");
    Serial.println("  SDA (SS) -> Pin 10");
    Serial.println("  SCK      -> Pin 13");
    Serial.println("  MOSI     -> Pin 11");
    Serial.println("  MISO     -> Pin 12");
    Serial.println("  RST      -> Pin 9");
    Serial.println("  3.3V     -> 3.3V (NOT 5V!)");
    Serial.println("  GND      -> GND");
    Serial.println("");
    Serial.println("Also verify:");
    Serial.println("  - RC522 is powered with 3.3V (not 5V)");
    Serial.println("  - All wires are connected securely");
    Serial.println("  - SPI pins are correct");
    while(1); // Halt
  } else {
    Serial.println("RC522 detected successfully!");
  }
  
  // Set antenna gain (try maximum)
  mfrc522.PCD_SetAntennaGain(mfrc522.RxGain_max);
  Serial.println("Antenna gain set to maximum");
  
  Serial.println("");
  Serial.println("RFID Reader Ready!");
  Serial.println("Place card near reader (1-2cm away)...");
  Serial.println("");
}

void loop() {
  // Look for new cards
  if (!mfrc522.PICC_IsNewCardPresent()) {
    // Optional: Print status every 5 seconds to show it's scanning
    static unsigned long lastStatus = 0;
    if (millis() - lastStatus > 5000) {
      Serial.println("Scanning... (place card near reader)");
      lastStatus = millis();
    }
    return;
  }
  
  Serial.println("Card detected! Reading...");
  
  // Select one of the cards
  if (!mfrc522.PICC_ReadCardSerial()) {
    Serial.println("ERROR: Failed to read card serial");
    return;
  }
  
  Serial.print("Card UID: ");
  
  // Get the UID (Unique Identifier) of the card
  String cardUID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) {
      cardUID += "0";
    }
    cardUID += String(mfrc522.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  
  // Print UID in multiple formats
  Serial.print(cardUID);
  Serial.print(" (size: ");
  Serial.print(mfrc522.uid.size);
  Serial.println(" bytes)");
  
  // Send ONLY the UID (for browser compatibility)
  Serial.println(cardUID);
  
  // Halt PICC (stop reading)
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  
  Serial.println("Card processed. Remove card and place another...");
  Serial.println("");
  
  // Small delay to prevent multiple reads
  delay(1000);
}
