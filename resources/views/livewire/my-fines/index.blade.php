<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Fines</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                View your library fines and payments
            </p>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Fines</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($totalPending, 2) }}</p>
                    </div>
                    <flux:icon.currency-dollar class="h-8 w-8 text-red-500" />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Paid</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <flux:icon.check class="h-8 w-8 text-green-500" />
                </div>
            </div>
        </div>

        <!-- Pending Fines Table -->
        @if($fines->count() > 0)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Pending Fines ({{ $fines->count() }})</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Book</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Type</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Due Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Description</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fines as $fine)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3">
                                            @if($fine->loan && $fine->loan->copy)
                                                <div class="font-medium">{{ $fine->loan->copy->book->title }}</div>
                                                <div class="text-sm text-zinc-500">{{ $fine->loan->copy->book->author }}</div>
                                            @else
                                                <span class="text-zinc-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <flux:badge color="{{ $fine->type === 'overdue' ? 'orange' : 'red' }}">
                                                {{ ucfirst($fine->type) }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold">${{ number_format($fine->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($fine->due_date)
                                                <span class="{{ $fine->due_date < now() ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                                    {{ $fine->due_date->format('M d, Y') }}
                                                </span>
                                                @if($fine->due_date < now())
                                                    <div class="text-xs text-red-600 dark:text-red-400">Overdue</div>
                                                @endif
                                            @else
                                                <span class="text-zinc-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $fine->description ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:badge color="orange">Pending</flux:badge>
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
                    <flux:icon.check class="h-12 w-12 text-green-400 mx-auto mb-4" />
                    <p class="text-lg font-semibold mb-2">No Pending Fines</p>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        You don't have any pending fines at the moment.
                    </p>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <h3 class="font-semibold mb-2">Fine Information</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                <li>Overdue fines: $0.50 per day</li>
                <li>Lost book replacement fee: $50.00</li>
                <li>Fines are due 30 days after creation</li>
                <li>Please contact the library to pay fines</li>
            </ul>
        </div>
    </div>
</flux:main>
