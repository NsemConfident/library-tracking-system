<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Back Button -->
        <flux:button :href="route('library.books.index')" variant="ghost" icon="arrow-left" wire:navigate>
            Back to Catalog
        </flux:button>

        <!-- Book Details -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Book Cover and Info -->
            <div class="md:col-span-1">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                    <div class="p-6">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                 alt="{{ $book->title }}"
                                 class="w-full rounded-lg mb-4">
                        @else
                            <div class="w-full aspect-[2/3] bg-zinc-200 dark:bg-zinc-700 rounded-lg mb-4 flex items-center justify-center">
                                <flux:icon.book-open class="h-24 w-24 text-zinc-400" />
                            </div>
                        @endif

                        <div class="space-y-2">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Status</p>
                                @if($book->available_copies_count > 0)
                                    <flux:badge color="green" class="mt-1">Available</flux:badge>
                                @else
                                    <flux:badge color="red" class="mt-1">Unavailable</flux:badge>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Copies</p>
                                <p class="font-semibold">
                                    {{ $book->available_copies_count }} of {{ $book->copies_count }} available
                                </p>
                            </div>

                            @if($book->category)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Category</p>
                                    <flux:badge color="gray" class="mt-1">{{ $book->category }}</flux:badge>
                                </div>
                            @endif
                        </div>

                        @if($book->available_copies_count === 0 && auth()->user()->isPatron())
                            <flux:button 
                                wire:click="placeHold" 
                                variant="primary" 
                                class="w-full mt-6"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Place Hold</span>
                                <span wire:loading>Processing...</span>
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Book Details -->
            <div class="md:col-span-2">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold mb-2">{{ $book->title }}</h1>
                        <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-6">by {{ $book->author }}</p>

                        @if($message)
                            <div class="mb-4 p-4 rounded-lg border {{ $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' }}">
                                {{ $message }}
                            </div>
                        @endif

                        @if($book->description)
                            <div class="mb-6">
                                <h3 class="font-semibold mb-2">Description</h3>
                                <p class="text-zinc-700 dark:text-zinc-300">{{ $book->description }}</p>
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            @if($book->isbn)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">ISBN</p>
                                    <p class="font-mono">{{ $book->isbn }}</p>
                                </div>
                            @endif

                            @if($book->publisher)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Publisher</p>
                                    <p>{{ $book->publisher }}</p>
                                </div>
                            @endif

                            @if($book->published_year)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Published</p>
                                    <p>{{ $book->published_year }}</p>
                                </div>
                            @endif

                            @if($book->pages)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Pages</p>
                                    <p>{{ number_format($book->pages) }}</p>
                                </div>
                            @endif

                            @if($book->language)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Language</p>
                                    <p>{{ strtoupper($book->language) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Copies List -->
                @if($book->copies->count() > 0)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 mt-6">
                        <div class="p-6">
                            <h3 class="font-semibold mb-4">Copies</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                            <th class="px-4 py-3 text-left text-sm font-medium">Barcode</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($book->copies as $copy)
                                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                                <td class="px-4 py-3 font-mono text-sm">{{ $copy->barcode }}</td>
                                                <td class="px-4 py-3">
                                                    <flux:badge color="{{ $copy->status === 'available' ? 'green' : ($copy->status === 'checked_out' ? 'orange' : 'gray') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $copy->status)) }}
                                                    </flux:badge>
                                                </td>
                                                <td class="px-4 py-3 text-sm">{{ $copy->location ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</flux:main>
