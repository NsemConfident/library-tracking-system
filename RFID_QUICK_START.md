# RFID Quick Start Guide

Quick reference for using RFID in the Library Tracking System.

## Setup Checklist

- [ ] Arduino Uno connected via USB
- [ ] RC522 wired correctly (3.3V power!)
- [ ] Arduino code uploaded
- [ ] Serial Monitor closed
- [ ] Browser open (Chrome/Edge)
- [ ] Laravel server running

## Quick Wiring Reference

```
RC522 → Arduino
SDA   → Pin 10
SCK   → Pin 13
MOSI  → Pin 11
MISO  → Pin 12
RST   → Pin 9
3.3V  → 3.3V  ⚠️ NOT 5V!
GND   → GND
```

## Quick Usage

### Return a Book
1. Go to `/library/circulation/return`
2. Click "Connect RFID Reader"
3. Select Arduino port
4. Place card near reader → Auto-submits!

### Checkout a Book
1. Go to `/library/circulation/checkout`
2. Click "Connect RFID Reader"
3. Select Arduino port
4. Select patron
5. Place card near reader → Fills barcode
6. Click "Checkout Book"

## Common Issues

| Problem | Solution |
|---------|----------|
| Can't connect | Close Serial Monitor |
| No card detected | Check 3.3V power, wiring |
| Works once then stops | Remove card, wait 2 sec, try again |
| Duplicate scans | Normal - debounce prevents within 2 sec |

## Testing Steps

1. **Arduino Test**: Serial Monitor → Place card → See UID
2. **Browser Test**: Connect → Place card → See UID in field
3. **Integration Test**: Scan → Form submits → Book processed

## Card Placement Tips

- Hold card **1-2cm** from reader
- Keep card **flat** and **parallel**
- Some cards need to be **very close**
- **Remove completely** after scanning

## Need Help?

See `RFID_IMPLEMENTATION.md` for detailed documentation.
