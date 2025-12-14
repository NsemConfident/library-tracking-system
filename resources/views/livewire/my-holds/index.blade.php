<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Holds</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                View and manage your book holds
            </p>
        </div>

        <!-- Message Display -->
        @if($message)
            <div class="p-4 rounded-lg border {{ $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' }}">
                {{ $message }}
            </div>
        @endif

        <!-- Holds Table -->
        @if($holds->count() > 0)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Active Holds ({{ $holds->count() }})</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Book</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Requested Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Expiry Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holds as $hold)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $hold->book->title }}</div>
                                            <div class="text-sm text-zinc-500">{{ $hold->book->author }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $hold->requested_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($hold->position)
                                                <flux:badge color="blue">#{{ $hold->position }}</flux:badge>
                                            @else
                                                <span class="text-zinc-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($hold->status === 'ready')
                                                <flux:badge color="green">Ready</flux:badge>
                                                <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                    Available for checkout
                                                </div>
                                            @elseif($hold->status === 'pending')
                                                <flux:badge color="orange">Pending</flux:badge>
                                            @else
                                                <flux:badge color="gray">{{ ucfirst($hold->status) }}</flux:badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($hold->expiry_date)
                                                <span class="{{ $hold->isExpired() ? 'text-red-600 dark:text-red-400' : '' }}">
                                                    {{ $hold->expiry_date->format('M d, Y') }}
                                                </span>
                                                @if($hold->isExpired())
                                                    <div class="text-xs text-red-600 dark:text-red-400">Expired</div>
                                                @endif
                                            @else
                                                <span class="text-zinc-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($hold->status === 'ready')
                                                <flux:button 
                                                    :href="route('library.books.show', $hold->book)" 
                                                    variant="primary" 
                                                    size="sm"
                                                    wire:navigate>
                                                    Checkout
                                                </flux:button>
                                            @endif
                                            @if(in_array($hold->status, ['pending', 'ready']))
                                                <flux:button 
                                                    wire:click="cancelHold({{ $hold->id }})" 
                                                    variant="outline" 
                                                    size="sm"
                                                    wire:loading.attr="disabled"
                                                    wire:target="cancelHold({{ $hold->id }})"
                                                    class="ml-2">
                                                    <span wire:loading.remove wire:target="cancelHold({{ $hold->id }})">Cancel</span>
                                                    <span wire:loading wire:target="cancelHold({{ $hold->id }})">Cancelling...</span>
                                                </flux:button>
                                            @endif
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
                    <flux:icon.clock class="h-12 w-12 text-zinc-400 mx-auto mb-4" />
                    <p class="text-lg font-semibold mb-2">No Active Holds</p>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        You don't have any active holds at the moment.
                    </p>
                    <flux:button :href="route('library.books.index')" variant="primary" wire:navigate>
                        Browse Catalog
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <h3 class="font-semibold mb-2">Hold Information</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                <li>Holds expire after 7 days if not fulfilled</li>
                <li>When a book becomes available, the first person in the queue will be notified</li>
                <li>You can cancel holds at any time</li>
                <li>Ready holds can be checked out immediately</li>
            </ul>
        </div>
    </div>
</flux:main>
