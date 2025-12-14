<flux:main>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Book Catalog</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Browse and search our collection
                </p>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by title, author, or ISBN..."
                        icon="magnifying-glass"
                    />

                    <flux:select wire:model.live="category" placeholder="All Categories">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Books Grid -->
        @if($books->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($books as $book)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 cursor-pointer hover:shadow-lg transition-shadow" 
                         wire:click="$dispatch('open-modal', { component: 'books.show', arguments: { book: {{ $book->id }} } })">
                        <div class="p-6">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                     alt="{{ $book->title }}"
                                     class="w-full h-48 object-cover rounded-lg mb-4">
                            @else
                                <div class="w-full h-48 bg-zinc-200 dark:bg-zinc-700 rounded-lg mb-4 flex items-center justify-center">
                                    <flux:icon.book-open class="h-12 w-12 text-zinc-400" />
                                </div>
                            @endif

                            <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $book->title }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">{{ $book->author }}</p>

                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    @if($book->available_copies_count > 0)
                                        <flux:badge color="green">Available</flux:badge>
                                    @else
                                        <flux:badge color="red">Unavailable</flux:badge>
                                    @endif
                                    @if($book->category)
                                        <flux:badge color="gray">{{ $book->category }}</flux:badge>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 text-xs text-zinc-500">
                                {{ $book->available_copies_count }} of {{ $book->copies_count }} copies available
                            </div>

                            <flux:button 
                                :href="route('library.books.show', $book)" 
                                variant="outline" 
                                class="w-full mt-4"
                                wire:navigate>
                                View Details
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                <div class="p-12 text-center">
                    <flux:icon.book-open class="h-12 w-12 text-zinc-400 mx-auto mb-4" />
                    <p class="text-lg font-semibold mb-2">No books found</p>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        @if($search || $category)
                            Try adjusting your search or filters
                        @else
                            No books in the catalog yet
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</flux:main>
