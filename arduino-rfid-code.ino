#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 10
#define RST_PIN 9

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance

void setup() {
  Serial.begin(9600);   // Initialize serial communication
  SPI.begin();          // Initialize SPI bus
  mfrc522.PCD_Init();    // Initialize MFRC522
  
  // Only print startup messages once (comment out for browser use)
  // Serial.println("RFID Reader Ready!");
  // Serial.println("Place card near reader...");
}

void loop() {
  // Look for new cards
  if (!mfrc522.PICC_IsNewCardPresent()) {
    // Reset reader state if no card present (helps with multiple scans)
    mfrc522.PCD_Init();
    delay(50);
    return;
  }
  
  // Select one of the cards
  if (!mfrc522.PICC_ReadCardSerial()) {
    // Failed to read, reset and try again
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
    delay(100);
    return;
  }
  
  // Get the UID (Unique Identifier) of the card
  String cardUID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) {
      cardUID += "0";
    }
    cardUID += String(mfrc522.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  
  // Send ONLY the UID (no extra text) - this is what the browser expects
  Serial.println(cardUID);
  
  // Properly halt and stop crypto
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  
  // Wait for card to be removed before scanning again
  // This prevents reading the same card multiple times
  unsigned long startTime = millis();
  while (mfrc522.PICC_IsNewCardPresent() || mfrc522.PICC_ReadCardSerial()) {
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
    delay(100);
    
    // Timeout after 5 seconds (in case card is left on reader)
    if (millis() - startTime > 5000) {
      break;
    }
  }
  
  // Small delay before next scan
  delay(200);
}

// Optional: Read data from card (if you program custom data)
void readCardData() {
  byte block = 4;  // Block to read (MIFARE cards have blocks)
  byte buffer[18];
  byte size = sizeof(buffer);
  
  if (mfrc522.MIFARE_Read(block, buffer, &size) == MFRC522::STATUS_OK) {
    Serial.print("Block ");
    Serial.print(block);
    Serial.print(": ");
    for (byte i = 0; i < 16; i++) {
      if (buffer[i] < 0x10) Serial.print("0");
      Serial.print(buffer[i], HEX);
      Serial.print(" ");
    }
    Serial.println();
  }
}