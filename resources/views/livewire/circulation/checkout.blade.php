<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Checkout Book</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Scan or enter barcode to checkout a book
            </p>
        </div>

        <!-- Checkout Form -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <form wire:submit="checkout" class="space-y-6">
                    <!-- Barcode Input -->
                    <flux:input
                        wire:model="barcode"
                        label="Book Barcode"
                        placeholder="Scan or enter barcode"
                        required
                        autofocus
                    />

                    <!-- User Search -->
                    <div>
                        <flux:input
                            wire:model.live.debounce.300ms="userSearch"
                            label="Search Patron"
                            placeholder="Search by name or email..."
                            icon="magnifying-glass"
                        />

                        @if($userSearch && $users->count() > 0)
                            <div class="mt-2 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                                @foreach($users as $user)
                                    <button
                                        type="button"
                                        wire:click="$set('selectedUserId', {{ $user->id }})"
                                        class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 last:border-b-0"
                                    >
                                        <div class="font-medium">{{ $user->name }}</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</div>
                                        @if($user->role)
                                            <flux:badge color="gray" class="mt-1 text-xs">{{ ucfirst($user->role) }}</flux:badge>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($selectedUserId)
                            @php
                                $selectedUser = \App\Models\User::find($selectedUserId);
                            @endphp
                            <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium">{{ $selectedUser->name }}</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $selectedUser->email }}</div>
                                    </div>
                                    <flux:button 
                                        type="button" 
                                        wire:click="$set('selectedUserId', null)" 
                                        variant="ghost" 
                                        size="sm">
                                        Change
                                    </flux:button>
                                </div>
                            </div>
                        @endif

                        @error('selectedUserId')
                            <p class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Loan Period -->
                    <flux:input
                        wire:model="loanDays"
                        label="Loan Period (days)"
                        type="number"
                        min="1"
                        max="90"
                        required
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
                        <span wire:loading.remove>Checkout Book</span>
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
                    <li>Search for the patron by name or email</li>
                    <li>Select the loan period (default: 14 days)</li>
                    <li>Click "Checkout Book" to complete the transaction</li>
                </ul>
            </div>
        </div>
    </div>
</flux:main>
