# RFID Implementation Guide

Complete guide for integrating Arduino Uno + RC522 RFID reader with the Library Tracking System.

## Table of Contents

1. [Hardware Requirements](#hardware-requirements)
2. [Hardware Setup](#hardware-setup)
3. [Arduino Code Setup](#arduino-code-setup)
4. [Laravel Integration](#laravel-integration)
5. [Testing](#testing)
6. [Usage](#usage)
7. [Troubleshooting](#troubleshooting)
8. [Technical Details](#technical-details)

---

## Hardware Requirements

### Components Needed

- **Arduino Uno** (or compatible)
- **RC522 RFID Reader Module** (13.56 MHz HF)
- **RFID Cards/Tags** (MIFARE Classic or Ultralight compatible)
- **Jumper Wires** (7 wires)
- **USB Cable** (for Arduino)

### Card Compatibility

The RC522 works with:
- ✅ MIFARE Classic (1K, 4K)
- ✅ MIFARE Ultralight
- ✅ Most 13.56 MHz HF cards
- ❌ UHF cards (900 MHz) - NOT compatible

---

## Hardware Setup

### Wiring Diagram

Connect RC522 to Arduino Uno:

```
RC522 Module    →    Arduino Uno
─────────────────────────────────
SDA (SS)       →    Pin 10
SCK            →    Pin 13
MOSI           →    Pin 11
MISO           →    Pin 12
RST            →    Pin 9
3.3V           →    3.3V  ⚠️ CRITICAL - NOT 5V!
GND            →    GND
```

### ⚠️ Important Notes

1. **Voltage**: RC522 MUST use 3.3V, NOT 5V
   - Using 5V can damage the module
   - Check your RC522 module - it should have a 3.3V pin
   - Some modules have both 3.3V and 5V pins - use 3.3V

2. **Wire Connections**: Ensure all wires are securely connected
   - Loose connections cause intermittent issues
   - Double-check pin assignments

3. **Power**: Arduino should be powered via USB
   - Ensure USB cable provides adequate power
   - Some USB ports don't provide enough power

---

## Arduino Code Setup

### Step 1: Install Required Library

1. Open Arduino IDE
2. Go to **Sketch → Include Library → Manage Libraries**
3. Search for **"MFRC522"** by GithubCommunity
4. Click **Install**
5. The **SPI** library is usually pre-installed

### Step 2: Upload Code

1. Open `arduino-rfid-code.ino` in Arduino IDE
2. Verify the code compiles (✓ button)
3. Select your board: **Tools → Board → Arduino Uno**
4. Select your port: **Tools → Port → [Your COM Port]**
5. Upload the code (→ button)
6. Wait for "Done uploading"

### Step 3: Verify Upload

1. Open Serial Monitor: **Tools → Serial Monitor**
2. Set baud rate to **9600**
3. You should see initialization messages (if enabled)
4. Place an RFID card near the reader
5. You should see the card UID printed (e.g., `524FCD5C`)

### Arduino Code Overview

The Arduino code:
- Initializes the RC522 reader
- Continuously scans for RFID cards
- Reads the card's UID (Unique Identifier)
- Sends the UID to the computer via Serial (USB)
- Outputs ONLY the UID (no extra text) for browser compatibility

**Key Features:**
- Properly resets reader state after each scan
- Waits for card removal before scanning again
- Prevents duplicate reads of the same card
- Handles errors gracefully

---

## Laravel Integration

### Files Added/Modified

#### New Files Created

1. **`resources/js/arduino-rfid.js`**
   - JavaScript handler for Web Serial API
   - Connects to Arduino via USB Serial
   - Reads RFID UIDs from Arduino
   - Updates Livewire components automatically

2. **`arduino-rfid-code.ino`**
   - Arduino sketch for RFID reading
   - Handles card detection and UID reading

3. **`arduino-rfid-diagnostic.ino`**
   - Diagnostic version with detailed error messages
   - Useful for troubleshooting hardware issues

#### Modified Files

1. **`resources/js/app.js`**
   - Added: `import './arduino-rfid.js';`

2. **`resources/views/livewire/circulation/checkout.blade.php`**
   - Added RFID connect button

3. **`resources/views/livewire/circulation/return-book.blade.php`**
   - Added RFID connect button

### How It Works

```
┌─────────────┐
│ RFID Card   │
│ (524FCD5C)  │
└──────┬──────┘
       │ Radio Waves (13.56 MHz)
       ▼
┌─────────────┐
│ RC522       │ Reads card UID
│ Reader      │
└──────┬──────┘
       │ USB Serial
       ▼
┌─────────────┐
│ Arduino Uno │ Sends UID via Serial
└──────┬──────┘
       │ USB Cable
       ▼
┌─────────────┐
│ Browser     │ Web Serial API reads UID
│ (Chrome)    │
└──────┬──────┘
       │ JavaScript
       ▼
┌─────────────┐
│ Livewire    │ Updates barcode field
│ Component   │ Auto-submits (Return page)
└─────────────┘
```

### Browser Requirements

- **Chrome** (recommended) or **Edge** (Chromium-based)
- Web Serial API support (not available in Firefox/Safari)
- Must be on `localhost` or `127.0.0.1` (HTTPS not required for localhost)

---

## Testing

### Step 1: Test Arduino Hardware

1. Upload Arduino code
2. Open Serial Monitor (9600 baud)
3. Place RFID card near reader (1-2cm away)
4. **Expected**: Card UID appears (e.g., `524FCD5C`)
5. Remove card
6. Place card again
7. **Expected**: UID appears again

**If Arduino doesn't detect cards:**
- Check wiring (especially 3.3V power)
- Verify card type (must be MIFARE compatible)
- Try moving card closer to reader
- Check if RC522 LED lights up

### Step 2: Test Browser Connection

1. **Close Serial Monitor** (critical - port can only be used by one program)
2. Start Laravel server: `php artisan serve`
3. Open browser: `http://localhost:8000`
4. Login as librarian
5. Navigate to: `/library/circulation/return`
6. Click **"🔌 Connect RFID Reader"**
7. Select Arduino COM port when prompted
8. **Expected**: Button changes to "✓ RFID Reader Connected"

**If connection fails:**
- Make sure Serial Monitor is closed
- Check Arduino is connected via USB
- Try different USB port
- Restart browser

### Step 3: Test RFID Scanning

1. With browser connected, place RFID card near reader
2. **Expected**:
   - Console log: `[ArduinoRFID] ✓ RFID Card Scanned! UID: 524FCD5C`
   - Barcode field fills with UID
   - Status shows: "Scanned: 524FCD5C"
   - On Return page: Form auto-submits

3. Remove card
4. Wait 2 seconds
5. Place card again
6. **Expected**: Card scans again (after debounce period)

### Step 4: Test Multiple Cards

1. Scan Card #1 → Should work
2. Remove Card #1
3. Scan Card #2 → Should work immediately
4. Scan Card #1 again → Should work (after debounce)

---

## Usage

### For Librarians

#### Checkout Process

1. Navigate to `/library/circulation/checkout`
2. Click **"Connect RFID Reader"** (if not already connected)
3. Select Arduino port when prompted
4. Search and select patron
5. Place RFID card near reader
6. Card UID appears in barcode field
7. Select loan period
8. Click **"Checkout Book"**

#### Return Process

1. Navigate to `/library/circulation/return`
2. Click **"Connect RFID Reader"** (if not already connected)
3. Place RFID card near reader
4. Card UID appears in barcode field
5. Form auto-submits
6. Book is returned automatically

### Best Practices

1. **Card Placement**
   - Hold card 1-2cm from reader
   - Keep card flat and parallel to reader
   - Some cards need to be very close

2. **Multiple Scans**
   - Remove card completely after scanning
   - Wait 1-2 seconds before placing again
   - Prevents duplicate reads

3. **Connection**
   - Connect once per session
   - Connection persists until page refresh
   - Reconnect if connection drops

4. **Error Handling**
   - If scan doesn't work, check browser console
   - Try disconnecting and reconnecting
   - Verify Arduino is still connected

---

## Troubleshooting

### Issue: Arduino Not Detecting Cards

**Symptoms:**
- Serial Monitor shows "RFID Reader Ready!" but no UID when card is placed

**Solutions:**
1. **Check Voltage**: RC522 must use 3.3V (not 5V)
2. **Check Wiring**: Verify all connections, especially SDA → Pin 10
3. **Card Type**: Ensure using MIFARE compatible card
4. **Distance**: Move card closer (1-2cm)
5. **Power**: Try different USB port or cable
6. **Upload Diagnostic Code**: Use `arduino-rfid-diagnostic.ino` to check RC522 version

**Diagnostic Output:**
- Version `0x00` or `0xFF` = RC522 not detected (wiring issue)
- Version `0x92` = RC522 detected correctly

### Issue: Browser Can't Connect

**Symptoms:**
- Error: "Could not open serial port"
- Port selection dialog doesn't appear

**Solutions:**
1. **Close Serial Monitor**: Port can only be used by one program
2. **Close Arduino IDE**: Sometimes IDE keeps port open
3. **Check Browser**: Must use Chrome or Edge
4. **Check URL**: Must be `localhost` or `127.0.0.1`
5. **Restart Browser**: Sometimes helps
6. **Unplug/Replug Arduino**: Resets USB connection

### Issue: Works First Time, Then Stops

**Symptoms:**
- First scan works perfectly
- Subsequent scans don't work

**Solutions:**
1. **Upload Updated Code**: Latest code handles multiple scans better
2. **Remove Card**: Card must be removed completely after scan
3. **Wait**: Wait 1-2 seconds before placing card again
4. **Disconnect/Reconnect**: Click button to disconnect, then reconnect
5. **Check Console**: Look for errors in browser console

### Issue: Same Card Scanned Multiple Times

**Symptoms:**
- Card triggers multiple form submissions
- Duplicate entries in system

**Solutions:**
- **This is Normal**: Debounce prevents duplicates within 2 seconds
- **Remove Card**: Card must be removed between scans
- **Wait**: Wait 3+ seconds to scan same card again

### Issue: UID Not Appearing in Barcode Field

**Symptoms:**
- Console shows UID scanned
- But barcode field doesn't update

**Solutions:**
1. **Check Livewire**: Make sure Livewire is loaded
2. **Check Console**: Look for JavaScript errors
3. **Refresh Page**: Reload the page
4. **Check Field**: Verify barcode input field exists on page

### Issue: Form Doesn't Auto-Submit (Return Page)

**Symptoms:**
- UID appears in field
- But form doesn't submit automatically

**Solutions:**
1. **Check Page**: Must be on `/library/circulation/return`
2. **Check Livewire**: Form must have `wire:submit="returnBook"`
3. **Manual Submit**: Can click "Return Book" button manually
4. **Check Console**: Look for JavaScript errors

---

## Technical Details

### RFID Card UID Format

- **Format**: Hexadecimal string (e.g., `524FCD5C`)
- **Length**: Typically 8 characters (4 bytes) for MIFARE Classic
- **Case**: Converted to uppercase for consistency
- **Storage**: Stored in `copies.barcode` field in database

### Serial Communication

- **Baud Rate**: 9600
- **Protocol**: USB Serial (Virtual COM Port)
- **Data Format**: One UID per line, terminated with newline (`\n`)
- **Example**: `524FCD5C\n`

### Web Serial API

- **Browser Support**: Chrome, Edge (Chromium-based)
- **Security**: Requires user permission to access serial port
- **Connection**: Persistent until page refresh or disconnect
- **Error Handling**: Automatic reconnection on errors

### Debouncing

- **Purpose**: Prevents duplicate scans of same card
- **Duration**: 2 seconds
- **Logic**: Same UID within 2 seconds is ignored
- **Reset**: Automatically resets after timeout

### Livewire Integration

- **Method**: Uses `Livewire.find()` and `component.set()`
- **Field**: Updates `barcode` property in Livewire component
- **Auto-Submit**: Triggers form submission on Return page
- **Fallback**: Direct DOM manipulation if Livewire not available

### Database Integration

- **Field**: `copies.barcode` (unique)
- **Format**: Stores RFID UID as string
- **Lookup**: `Copy::where('barcode', $uid)->first()`
- **Compatibility**: Works with existing barcode system

---

## File Structure

```
library-tracking-system/
├── arduino-rfid-code.ino          # Main Arduino code
├── arduino-rfid-diagnostic.ino    # Diagnostic version
├── resources/
│   └── js/
│       ├── app.js                 # Imports arduino-rfid.js
│       └── arduino-rfid.js        # Browser RFID handler
├── resources/views/livewire/circulation/
│   ├── checkout.blade.php         # Has RFID button
│   └── return-book.blade.php      # Has RFID button
└── RFID_IMPLEMENTATION.md         # This file
```

---

## Security Considerations

1. **Serial Port Access**: Requires user permission (browser prompt)
2. **Localhost Only**: Web Serial API works on localhost without HTTPS
3. **No Network Exposure**: Arduino connection is local USB only
4. **Input Validation**: UID format validated before processing
5. **Debouncing**: Prevents rapid duplicate submissions

---

## Future Enhancements

Possible improvements:

1. **Multiple Readers**: Support multiple Arduino readers
2. **Batch Scanning**: Scan multiple books at once
3. **Exit Gate Integration**: Fixed reader at library exit
4. **Mobile App**: Native mobile app with Bluetooth
5. **Tag Programming**: Write custom data to RFID tags
6. **Inventory Mode**: Scan entire shelf for inventory

---

## Support

### Common Questions

**Q: Can I use a different RFID reader?**
A: Yes, but code may need modification. RC522 is recommended for simplicity.

**Q: Can I use UHF RFID tags?**
A: No, RC522 only works with HF (13.56 MHz). For UHF, you'd need a different reader.

**Q: Does it work on mobile devices?**
A: No, Web Serial API is desktop-only. Mobile would need a native app.

**Q: Can I use multiple Arduinos?**
A: Currently no, but could be added with port selection.

**Q: What if I lose the RFID tag?**
A: You can manually enter the barcode, or mark copy as missing and add new tag.

### Getting Help

1. Check browser console for errors (F12)
2. Check Arduino Serial Monitor for hardware issues
3. Review troubleshooting section above
4. Verify all wiring connections
5. Test with diagnostic code first

---

## Version History

- **v1.0** (Current)
  - Initial implementation
  - Arduino Uno + RC522 support
  - Web Serial API integration
  - Auto-detection for checkout/return
  - Debouncing for duplicate prevention

---

## License

This implementation is part of the Library Tracking System project.

---

**Last Updated**: 2024
**Maintained By**: Library System Development Team
