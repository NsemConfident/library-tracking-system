<?php

namespace App\Livewire\Circulation;

use App\Models\Copy;
use App\Models\User;
use App\Services\LibraryService;
use Livewire\Component;

class Checkout extends Component
{
    public $barcode = '';
    public $userSearch = '';
    public $selectedUserId = null;
    public $loanDays = 14;
    public $message = '';
    public $messageType = '';

    protected $listeners = ['userSelected'];

    public function userSelected($userId)
    {
        $this->selectedUserId = $userId;
    }

    public function checkout()
    {
        $this->validate([
            'barcode' => 'required|string',
            'selectedUserId' => 'required|exists:users,id',
            'loanDays' => 'required|integer|min:1|max:90',
        ]);

        try {
            $copy = Copy::where('barcode', $this->barcode)->firstOrFail();
            $user = User::findOrFail($this->selectedUserId);

            $loan = app(LibraryService::class)->checkout($copy, $user, $this->loanDays);

            $this->message = "Book '{$loan->copy->book->title}' checked out successfully to {$user->name}";
            $this->messageType = 'success';
            
            $this->reset(['barcode', 'selectedUserId', 'userSearch', 'loanDays']);
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        $users = [];
        if ($this->userSearch) {
            $users = User::where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%")
                ->take(10)
                ->get();
        }

        return view('livewire.circulation.checkout', [
            'users' => $users,
        ])->layout('components.layouts.app.sidebar', [
            'title' => __('Checkout Book'),
        ]);
    }
}
