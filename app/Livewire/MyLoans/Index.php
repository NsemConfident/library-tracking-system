<?php

namespace App\Livewire\MyLoans;

use App\Models\Loan;
use App\Services\LibraryService;
use Livewire\Component;

class Index extends Component
{
    public $message = '';
    public $messageType = '';
    public $renewingLoanId = null;

    public function renew($loanId)
    {
        $this->renewingLoanId = $loanId;
        
        try {
            $loan = Loan::where('id', $loanId)
                ->where('user_id', auth()->id())
                ->where('status', 'active')
                ->firstOrFail();

            $loan = app(LibraryService::class)->renew($loan, 14);
            
            $this->message = "Loan renewed successfully. New due date: {$loan->due_date->format('M d, Y')}";
            $this->messageType = 'success';
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        } finally {
            $this->renewingLoanId = null;
        }
    }

    public function render()
    {
        $loans = auth()->user()
            ->loans()
            ->with(['copy.book'])
            ->where('status', 'active')
            ->orderBy('due_date')
            ->get();

        return view('livewire.my-loans.index', [
            'loans' => $loans,
        ])->layout('components.layouts.app.sidebar', [
            'title' => __('My Loans'),
        ]);
    }
}
