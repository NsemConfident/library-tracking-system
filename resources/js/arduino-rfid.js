// Simple Arduino RFID → barcode field bridge using Web Serial API
// Assumes Arduino sends one UID per line over Serial (e.g. "A1B2C3D4\n")

class ArduinoRFID {
    constructor() {
        this.port = null;
        this.reader = null;
        this.isReading = false;
    }

    async connect() {
        if (!('serial' in navigator)) {
            alert('Web Serial API not supported. Use Chrome or Edge on desktop.');
            return;
        }

        try {
            // Ask user to choose the Arduino serial port
            this.port = await navigator.serial.requestPort();
            await this.port.open({ baudRate: 9600 });

            const textDecoder = new TextDecoderStream();
            const readableStreamClosed = this.port.readable.pipeTo(textDecoder.writable);
            void readableStreamClosed; // silence unused variable lint

            this.reader = textDecoder.readable.getReader();
            this.isReading = true;

            console.log('[ArduinoRFID] Connected to serial port');
            this.readLoop();
        } catch (error) {
            console.error('[ArduinoRFID] Failed to open serial port', error);
            alert('Could not open serial port. Make sure the Arduino is connected and not used by another program.');
        }
    }

    async readLoop() {
        let buffer = '';

        while (this.isReading && this.reader) {
            try {
                const { value, done } = await this.reader.read();
                if (done) {
                    break;
                }

                if (value) {
                    buffer += value;

                    // Process complete lines
                    const lines = buffer.split(/\r?\n/);
                    buffer = lines.pop() ?? '';

                    for (const rawLine of lines) {
                        const line = rawLine.trim();
                        if (!line) continue;

                        // Expect hex UID like "A1B2C3D4"
                        if (/^[0-9a-fA-F]+$/.test(line)) {
                            this.handleTag(line.toUpperCase());
                        } else {
                            console.warn('[ArduinoRFID] Ignoring non-UID line from serial:', line);
                        }
                    }
                }
            } catch (error) {
                console.error('[ArduinoRFID] Error while reading from serial', error);
                break;
            }
        }

        this.isReading = false;
    }

    handleTag(uid) {
        console.log('[ArduinoRFID] UID scanned:', uid);

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
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    initArduinoRFIDOnPage();
});

// Handle Livewire's client-side navigation (wire:navigate)
window.addEventListener('livewire:navigated', () => {
    initArduinoRFIDOnPage();
});


