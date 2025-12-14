<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Loans</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                View and manage your active book loans
            </p>
        </div>

        <!-- Message Display -->
        @if($message)
            <div class="p-4 rounded-lg border {{ $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' }}">
                {{ $message }}
            </div>
        @endif

        <!-- Loans Table -->
        @if($loans->count() > 0)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Active Loans ({{ $loans->count() }})</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Book</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Checkout Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Due Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $loan->copy->book->title }}</div>
                                            <div class="text-sm text-zinc-500">{{ $loan->copy->book->author }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $loan->checkout_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="{{ $loan->isOverdue() ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                                {{ $loan->due_date->format('M d, Y') }}
                                            </span>
                                            @if($loan->isOverdue())
                                                <div class="text-xs text-red-600 dark:text-red-400">
                                                    {{ $loan->days_overdue }} day(s) overdue
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($loan->isOverdue())
                                                <flux:badge color="red">Overdue</flux:badge>
                                            @else
                                                <flux:badge color="green">Active</flux:badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:button 
                                                wire:click="renew({{ $loan->id }})" 
                                                variant="outline" 
                                                size="sm"
                                                wire:loading.attr="disabled"
                                                wire:target="renew({{ $loan->id }})">
                                                <span wire:loading.remove wire:target="renew({{ $loan->id }})">Renew</span>
                                                <span wire:loading wire:target="renew({{ $loan->id }})">Renewing...</span>
                                            </flux:button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <div class="p-12 text-center">
                    <flux:icon.book-open class="h-12 w-12 text-zinc-400 mx-auto mb-4" />
                    <p class="text-lg font-semibold mb-2">No Active Loans</p>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        You don't have any active loans at the moment.
                    </p>
                    <flux:button :href="route('library.books.index')" variant="primary" wire:navigate>
                        Browse Catalog
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <h3 class="font-semibold mb-2">Loan Information</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                <li>You can renew loans up to 14 days before the due date</li>
                <li>Books with pending holds cannot be renewed</li>
                <li>Overdue books may incur fines</li>
                <li>Maximum of 10 active loans at a time</li>
            </ul>
        </div>
    </div>
</flux:main>
