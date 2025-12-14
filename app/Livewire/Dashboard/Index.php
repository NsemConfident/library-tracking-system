<?php

namespace App\Livewire\Dashboard;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Hold;
use App\Models\Fine;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        $stats = [
            'total_books' => Book::count(),
            'total_copies' => \App\Models\Copy::count(),
            'available_copies' => \App\Models\Copy::where('status', 'available')->count(),
        ];

        if ($user->isLibrarian()) {
            $stats['active_loans'] = Loan::where('status', 'active')->count();
            $stats['overdue_loans'] = Loan::where('status', 'active')
                ->where('due_date', '<', now())
                ->count();
            $stats['pending_holds'] = Hold::where('status', 'pending')->count();
            $stats['pending_fines'] = Fine::where('status', 'pending')->sum('amount');
        } else {
            $stats['my_loans'] = $user->activeLoans()->count();
            $stats['my_holds'] = $user->holds()->whereIn('status', ['pending', 'ready'])->count();
            $stats['my_fines'] = $user->pendingFines()->sum('amount');
        }

        $recentLoans = $user->isLibrarian()
            ? Loan::with(['copy.book', 'user'])->latest()->take(10)->get()
            : $user->loans()->with('copy.book')->latest()->take(10)->get();

        return view('livewire.dashboard.index', [
            'stats' => $stats,
            'recentLoans' => $recentLoans,
        ]);
    }
}
