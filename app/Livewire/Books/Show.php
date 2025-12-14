<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Services\LibraryService;
use Livewire\Component;

class Show extends Component
{
    public Book $book;
    public $message = '';
    public $messageType = '';

    public function mount(Book $book)
    {
        $this->book = $book->load(['copies', 'holds.user'])->loadCount(['copies', 'availableCopies']);
    }

    public function placeHold()
    {
        try {
            $hold = app(LibraryService::class)->placeHold($this->book, auth()->user());
            $this->message = 'Hold placed successfully. You will be notified when the book is available.';
            $this->messageType = 'success';
            $this->book->refresh();
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.books.show')->layout('components.layouts.app.sidebar', [
            'title' => $this->book->title,
        ]);
    }
}
