// Simple Arduino RFID → barcode field bridge using Web Serial API
// Assumes Arduino sends one UID per line over Serial (e.g. "A1B2C3D4\n")

class ArduinoRFID {
    constructor() {
        this.port = null;
        this.reader = null;
        this.isReading = false;
        this.lastScannedUID = null;
        this.lastScanTime = 0;
        this.SCAN_DEBOUNCE_MS = 2000; // Don't process same UID within 2 seconds
    }

    async connect() {
        if (!('serial' in navigator)) {
            alert('Web Serial API not supported. Use Chrome or Edge on desktop.');
            return;
        }

        try {
            // Ask user to choose the Arduino serial port
            this.port = await navigator.serial.requestPort();
            
            console.log('[ArduinoRFID] Selected port:', this.port);
            
            // Try to open the port
            await this.port.open({ baudRate: 9600 });
            console.log('[ArduinoRFID] Port opened successfully');

            const textDecoder = new TextDecoderStream();
            const readableStreamClosed = this.port.readable.pipeTo(textDecoder.writable);
            void readableStreamClosed; // silence unused variable lint

            this.reader = textDecoder.readable.getReader();
            this.isReading = true;

            console.log('[ArduinoRFID] Connected to serial port');
            this.readLoop();
        } catch (error) {
            console.error('[ArduinoRFID] Failed to open serial port', error);
            
            let errorMessage = 'Could not open serial port.\n\n';
            
            if (error.name === 'NotFoundError') {
                errorMessage += 'No port was selected. Please try again and select your Arduino COM port.';
            } else if (error.name === 'SecurityError') {
                errorMessage += 'Permission denied. Please allow access to the serial port.';
            } else if (error.message && error.message.includes('already')) {
                errorMessage += 'Port is already in use!\n\n';
                errorMessage += 'TROUBLESHOOTING:\n';
                errorMessage += '1. Close Arduino IDE Serial Monitor if it\'s open\n';
                errorMessage += '2. Close any other programs using the Arduino\n';
                errorMessage += '3. Unplug and replug the Arduino USB cable\n';
                errorMessage += '4. Refresh this page and try again';
            } else {
                errorMessage += 'TROUBLESHOOTING:\n';
                errorMessage += '1. Make sure Arduino is connected via USB\n';
                errorMessage += '2. Close Arduino IDE Serial Monitor\n';
                errorMessage += '3. Close any other programs using the Arduino\n';
                errorMessage += '4. Try unplugging and replugging the Arduino\n';
                errorMessage += '5. Make sure Arduino code is uploaded and running\n';
                errorMessage += '\nError details: ' + error.message;
            }
            
            alert(errorMessage);
        }
    }

    async readLoop() {
        let buffer = '';
        let lastActivityTime = Date.now();
        const ACTIVITY_TIMEOUT = 30000; // 30 seconds

        console.log('[ArduinoRFID] Read loop started, waiting for data...');
        console.log('[ArduinoRFID] Place an RFID card near the reader to test...');

        while (this.isReading && this.reader) {
            try {
                // Check for timeout (no data for 30 seconds)
                if (Date.now() - lastActivityTime > ACTIVITY_TIMEOUT) {
                    console.log('[ArduinoRFID] No data received for 30 seconds. Still listening...');
                    lastActivityTime = Date.now();
                }

                const { value, done } = await this.reader.read();
                if (done) {
                    console.log('[ArduinoRFID] Read stream ended');
                    break;
                }

                if (value) {
                    lastActivityTime = Date.now();
                    
                    // Log ALL raw data for debugging
                    console.log('[ArduinoRFID] ✓ Data received! Raw:', JSON.stringify(value));
                    console.log('[ArduinoRFID] Data type:', typeof value, 'Length:', value.length);
                    
                    buffer += value;
                    console.log('[ArduinoRFID] Current buffer:', JSON.stringify(buffer));

                    // Process complete lines (split by newline or carriage return)
                    const lines = buffer.split(/\r?\n/);
                    buffer = lines.pop() ?? '';

                    console.log('[ArduinoRFID] Split into', lines.length, 'complete line(s)');

                    for (const rawLine of lines) {
                        const line = rawLine.trim();
                        console.log('[ArduinoRFID] → Processing line:', JSON.stringify(line), `(length: ${line.length})`);
                        
                        if (!line) {
                            console.log('[ArduinoRFID] Empty line, skipping');
                            continue;
                        }

                        // Expect hex UID - typically 4-16 hex characters (2-8 bytes)
                        // Common formats: "A1B2C3D4" (4 bytes = 8 hex chars) or longer
                        const hexPattern = /^[0-9a-fA-F]{4,16}$/;
                        
                        if (hexPattern.test(line)) {
                            console.log('[ArduinoRFID] ✓✓✓ VALID UID DETECTED:', line.toUpperCase());
                            this.handleTag(line.toUpperCase());
                        } else {
                            // Log all non-UID lines for debugging
                            console.log('[ArduinoRFID] Not a valid UID. Line:', JSON.stringify(line));
                            
                            // Try to extract hex from the line (in case there's extra whitespace/chars)
                            const hexMatch = line.match(/[0-9a-fA-F]{4,16}/i);
                            if (hexMatch) {
                                const extractedUID = hexMatch[0];
                                console.log('[ArduinoRFID] ✓ Extracted UID from line:', extractedUID.toUpperCase());
                                this.handleTag(extractedUID.toUpperCase());
                            } else {
                                console.log('[ArduinoRFID] Could not extract UID from:', JSON.stringify(line));
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('[ArduinoRFID] ✗ Error while reading from serial:', error);
                console.error('[ArduinoRFID] Error details:', error.name, error.message);
                break;
            }
        }

        console.log('[ArduinoRFID] Read loop ended');
        this.isReading = false;
    }

    handleTag(uid) {
        // Debounce: ignore if same UID scanned recently
        const now = Date.now();
        if (uid === this.lastScannedUID && (now - this.lastScanTime) < this.SCAN_DEBOUNCE_MS) {
            console.log('[ArduinoRFID] Ignoring duplicate scan (debounce):', uid);
            return;
        }
        
        this.lastScannedUID = uid;
        this.lastScanTime = now;
        
        console.log('[ArduinoRFID] ✓ RFID Card Scanned! UID:', uid);
        
        // Show visual feedback
        const statusSpan = document.getElementById('rfid-status');
        if (statusSpan) {
            statusSpan.textContent = `Scanned: ${uid}`;
            statusSpan.className = 'ml-3 text-sm text-green-600 dark:text-green-400 font-medium';
            // Clear after 3 seconds
            setTimeout(() => {
                if (statusSpan.textContent.includes('Scanned:')) {
                    statusSpan.textContent = 'Ready to scan';
                }
            }, 3000);
        }

        // Try to find the Livewire barcode input
        const barcodeInput =
            document.querySelector('input[wire\\:model=\"barcode\"]') ||
            document.querySelector('input[wire\\:model*=\"barcode\"]') ||
            document.querySelector('input[name*=\"barcode\"]');

        if (!barcodeInput) {
            alert(`Scanned ${uid}, but no barcode field was found on this page.`);
            return;
        }

        // Update Livewire component using Livewire's API
        const wireId = barcodeInput.getAttribute('wire:id') || 
                      barcodeInput.closest('[wire\\:id]')?.getAttribute('wire:id');
        
        if (wireId && window.Livewire) {
            const component = window.Livewire.find(wireId);
            if (component) {
                // Use Livewire's set method to update the barcode property
                component.set('barcode', uid);
                console.log('[ArduinoRFID] Updated Livewire component barcode:', uid);
            } else {
                // Fallback: direct input update
                barcodeInput.value = uid;
                barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
                barcodeInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } else {
            // Fallback: direct input update
            barcodeInput.value = uid;
            barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            barcodeInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const path = window.location.pathname;
        const isReturnPage = path.includes('/circulation/return');

        // On the Return page, auto-submit the form after a short delay
        if (isReturnPage) {
            setTimeout(() => {
                const form = barcodeInput.closest('form');
                if (form) {
                    // Use requestSubmit so the existing wire:submit handler runs
                    form.requestSubmit();
                }
            }, 300); // Small delay to ensure Livewire has updated
        }
    }

    async disconnect() {
        this.isReading = false;
        this.lastScannedUID = null; // Reset debounce

        try {
            if (this.reader) {
                await this.reader.cancel();
                await this.reader.releaseLock();
            }

            if (this.port) {
                await this.port.close();
            }
        } catch (error) {
            console.error('[ArduinoRFID] Error while disconnecting', error);
        } finally {
            this.reader = null;
            this.port = null;
        }
    }
    
    // Reset debounce (useful for testing)
    resetDebounce() {
        this.lastScannedUID = null;
        this.lastScanTime = 0;
        console.log('[ArduinoRFID] Debounce reset - ready for new scan');
    }
}

function initArduinoRFIDOnPage() {
    const path = window.location.pathname;
    const isCheckoutPage = path.includes('/circulation/checkout');
    const isReturnPage = path.includes('/circulation/return');

    if (!isCheckoutPage && !isReturnPage) {
        return;
    }

    // Reuse existing instance if present
    let rfid = window.arduinoRFID;
    if (!rfid) {
        rfid = new ArduinoRFID();
        window.arduinoRFID = rfid; // expose for debugging
    }

    // Find the button (now it's in the Blade template)
    const btn = document.getElementById('rfid-connect-btn');
    const statusSpan = document.getElementById('rfid-status');
    
    if (!btn) {
        console.warn('[ArduinoRFID] Connect button not found on page');
        return;
    }

    // Update button text and status based on connection state
    const updateUI = (connected) => {
        if (connected) {
            btn.textContent = '✓ RFID Reader Connected';
            btn.className = 'px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium transition-colors';
            if (statusSpan) {
                statusSpan.textContent = 'Ready to scan';
                statusSpan.className = 'ml-3 text-sm text-green-600 dark:text-green-400';
            }
        } else {
            btn.textContent = '🔌 Connect RFID Reader';
            btn.className = 'px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors';
            if (statusSpan) {
                statusSpan.textContent = '';
            }
        }
    };

    // Avoid attaching multiple listeners
    if (!btn.dataset.rfidBound) {
        btn.addEventListener('click', async () => {
            if (rfid.isReading) {
                // Disconnect
                await rfid.disconnect();
                updateUI(false);
            } else {
                // Connect
                try {
                    await rfid.connect();
                    updateUI(true);
                } catch (error) {
                    updateUI(false);
                    if (statusSpan) {
                        statusSpan.textContent = 'Connection failed';
                        statusSpan.className = 'ml-3 text-sm text-red-600 dark:text-red-400';
                    }
                }
            }
        });
        btn.dataset.rfidBound = 'true';
    }
    
            // Update UI based on current state
    updateUI(rfid.isReading);
    
    // Add test button for debugging
    if (isCheckoutPage || isReturnPage) {
        let testBtn = document.getElementById('rfid-test-btn');
        if (!testBtn && btn) {
            testBtn = document.createElement('button');
            testBtn.id = 'rfid-test-btn';
            testBtn.type = 'button';
            testBtn.textContent = 'Test: Send Test UID';
            testBtn.className = 'px-4 py-2 ml-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium transition-colors';
            testBtn.onclick = () => {
                console.log('[ArduinoRFID] Manual test: Simulating UID scan');
                rfid.handleTag('TEST1234');
            };
            btn.parentElement.appendChild(testBtn);
        }
    }
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    initArduinoRFIDOnPage();
});

// Handle Livewire's client-side navigation (wire:navigate)
window.addEventListener('livewire:navigated', () => {
    initArduinoRFIDOnPage();
});


