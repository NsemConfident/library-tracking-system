<?php

namespace App\Livewire\Circulation;

use App\Models\Loan;
use App\Services\LibraryService;
use Livewire\Component;

class ReturnBook extends Component
{
    public $barcode = '';
    public $message = '';
    public $messageType = '';

    public function returnBook()
    {
        $this->validate([
            'barcode' => 'required|string',
        ]);

        try {
            $copy = \App\Models\Copy::where('barcode', $this->barcode)->firstOrFail();
            $loan = Loan::where('copy_id', $copy->id)
                ->where('status', 'active')
                ->firstOrFail();

            $loan = app(LibraryService::class)->return($loan);

            $this->message = "Book '{$loan->copy->book->title}' returned successfully";
            $this->messageType = 'success';
            
            $this->reset('barcode');
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.circulation.return-book');
    }
}
