<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    protected $queryString = ['search', 'category'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Book::withCount(['copies', 'availableCopies']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('author', 'like', "%{$this->search}%")
                    ->orWhere('isbn', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $books = $query->orderBy('title')->paginate(20);
        $categories = Book::distinct()->pluck('category')->filter();

        return view('livewire.books.index', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }
}
