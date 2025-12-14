<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                Welcome back, {{ auth()->user()->name }}!
            </h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                @if(auth()->user()->isLibrarian())
                    Library Management Dashboard
                @else
                    Your Library Dashboard
                @endif
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @if(auth()->user()->isLibrarian())
                <!-- Librarian Stats -->
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Books</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['total_books']) }}</p>
                        </div>
                        <flux:icon.book-open class="h-8 w-8 text-blue-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Active Loans</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['active_loans']) }}</p>
                            <p class="text-xs text-red-600 dark:text-red-400">
                                {{ $stats['overdue_loans'] }} overdue
                            </p>
                        </div>
                        <flux:icon.arrow-right-circle class="h-8 w-8 text-orange-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Holds</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['pending_holds']) }}</p>
                        </div>
                        <flux:icon.clock class="h-8 w-8 text-purple-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Fines</p>
                            <p class="text-2xl font-bold">${{ number_format($stats['pending_fines'], 2) }}</p>
                        </div>
                        <flux:icon.currency-dollar class="h-8 w-8 text-red-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Copies</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['total_copies']) }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400">
                                {{ $stats['available_copies'] }} available
                            </p>
                        </div>
                        <flux:icon.document-duplicate class="h-8 w-8 text-green-500" />
                    </div>
                </div>
            @else
                <!-- Patron Stats -->
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">My Active Loans</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['my_loans']) }}</p>
                        </div>
                        <flux:icon.book-open class="h-8 w-8 text-blue-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">My Holds</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['my_holds']) }}</p>
                        </div>
                        <flux:icon.clock class="h-8 w-8 text-purple-500" />
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Outstanding Fines</p>
                            <p class="text-2xl font-bold">${{ number_format($stats['my_fines'], 2) }}</p>
                        </div>
                        <flux:icon.currency-dollar class="h-8 w-8 text-red-500" />
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Loans Table -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Recent Loans</h2>
                
                @if($recentLoans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Book</th>
                                    @if(auth()->user()->isLibrarian())
                                        <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Patron</th>
                                    @endif
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Checkout Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Due Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLoans as $loan)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $loan->copy->book->title }}</div>
                                            <div class="text-sm text-zinc-500">{{ $loan->copy->book->author }}</div>
                                        </td>
                                        @if(auth()->user()->isLibrarian())
                                            <td class="px-4 py-3 text-sm">{{ $loan->user->name }}</td>
                                        @endif
                                        <td class="px-4 py-3 text-sm">{{ $loan->checkout_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="{{ $loan->due_date < now() && $loan->status === 'active' ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                                {{ $loan->due_date->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($loan->status === 'active')
                                                <flux:badge color="{{ $loan->due_date < now() ? 'red' : 'green' }}">
                                                    {{ $loan->due_date < now() ? 'Overdue' : 'Active' }}
                                                </flux:badge>
                                            @else
                                                <flux:badge color="gray">{{ ucfirst($loan->status) }}</flux:badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-zinc-500 py-8">No recent loans</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if(auth()->user()->isLibrarian())
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex flex-col gap-3">
                        <flux:button :href="route('library.circulation.checkout')" variant="primary" wire:navigate>
                            Checkout Book
                        </flux:button>
                        <flux:button :href="route('library.circulation.return')" variant="outline" wire:navigate>
                            Return Book
                        </flux:button>
                        <flux:button :href="route('library.books.index')" variant="outline" wire:navigate>
                            Browse Catalog
                        </flux:button>
                    </div>
                </div>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex flex-col gap-3">
                        <flux:button :href="route('library.books.index')" variant="primary" wire:navigate>
                            Browse Catalog
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>
