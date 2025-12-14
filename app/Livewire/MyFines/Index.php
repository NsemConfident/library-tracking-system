<?php

namespace App\Livewire\MyFines;

use App\Models\Fine;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $fines = auth()->user()
            ->fines()
            ->with(['loan.copy.book'])
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        $totalPending = $fines->sum('amount');
        $totalPaid = auth()->user()
            ->fines()
            ->where('status', 'paid')
            ->sum('amount');

        return view('livewire.my-fines.index', [
            'fines' => $fines,
            'totalPending' => $totalPending,
            'totalPaid' => $totalPaid,
        ])->layout('components.layouts.app.sidebar', [
            'title' => __('My Fines'),
        ]);
    }
}
