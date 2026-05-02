<flux:main>
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RFID Circulation</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Scan student card to start/end a library session, then scan books.
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <div class="mb-4">
                    <button
                        type="button"
                        id="rfid-connect-btn"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                        🔌 Connect RFID Reader
                    </button>
                    <span id="rfid-status" class="ml-3 text-sm text-zinc-600 dark:text-zinc-400"></span>
                </div>

                <div class="space-y-5">
                    <div class="p-4 rounded-lg border {{ $activeSession ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700' }}">
                        @if($activeSession)
                            <p class="font-medium text-green-800 dark:text-green-200">
                                Active session: {{ $activeSession->user->name }}
                            </p>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                Checked in at {{ $activeSession->check_in_time->format('h:i A') }}
                            </p>
                        @else
                            <p class="font-medium text-zinc-800 dark:text-zinc-100">No active session</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                Scan a student RFID card to start.
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:button type="button" wire:click="setMode('borrow')" :variant="$mode === 'borrow' ? 'primary' : 'ghost'">
                            Borrow Mode
                        </flux:button>
                        <flux:button type="button" wire:click="setMode('return')" :variant="$mode === 'return' ? 'primary' : 'ghost'">
                            Return Mode
                        </flux:button>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">
                            Current mode: <span class="font-medium uppercase">{{ $mode }}</span>
                        </span>
                    </div>

                    <flux:input
                        wire:model="barcode"
                        label="Last Scanned UID"
                        placeholder="Scan student or book RFID"
                        readonly
                    />

                    <flux:input
                        wire:model="loanDays"
                        label="Loan Period (days)"
                        type="number"
                        min="1"
                        max="90"
                    />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <h3 class="font-semibold mb-2">Instructions</h3>
                <ul class="list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                    <li>Scan a student card to check in and start a session</li>
                    <li>Choose Borrow Mode or Return Mode</li>
                    <li>Scan one or more book RFID tags</li>
                    <li>Scan the same student card again to check out and close the session</li>
                </ul>
            </div>
        </div>
    </div>
</flux:main>
