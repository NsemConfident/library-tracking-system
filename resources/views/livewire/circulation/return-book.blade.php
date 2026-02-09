<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Return Book</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Scan or enter barcode to return a book
            </p>
        </div>

        <!-- Return Form -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <!-- RFID Connect Button -->
                <div class="mb-4">
                    <button 
                        type="button" 
                        id="rfid-connect-btn"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                        🔌 Connect RFID Reader
                    </button>
                    <span id="rfid-status" class="ml-3 text-sm text-zinc-600 dark:text-zinc-400"></span>
                </div>
                
                <form wire:submit="returnBook" class="space-y-6">
                    <!-- Barcode Input -->
                    <flux:input
                        wire:model="barcode"
                        label="Book Barcode"
                        placeholder="Scan or enter barcode"
                        required
                        autofocus
                    />

                    <!-- Message Display -->
                    @if($message)
                        <div class="p-4 rounded-lg border {{ $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' }}">
                            {{ $message }}
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <flux:button 
                        type="submit" 
                        variant="primary" 
                        class="w-full"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Return Book</span>
                        <span wire:loading>Processing...</span>
                    </flux:button>
                </form>
            </div>
        </div>

        <!-- Help Text -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <h3 class="font-semibold mb-2">Instructions</h3>
                <ul class="list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                    <li>Scan or manually enter the book barcode</li>
                    <li>The system will automatically process the return</li>
                    <li>Any overdue fines will be calculated and added</li>
                    <li>If there are pending holds, the next patron will be notified</li>
                </ul>
            </div>
        </div>
    </div>
</flux:main>
