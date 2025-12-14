<?php

namespace App\Livewire\MyHolds;

use App\Models\Hold;
use App\Services\LibraryService;
use Livewire\Component;

class Index extends Component
{
    public $message = '';
    public $messageType = '';
    public $cancellingHoldId = null;

    public function cancelHold($holdId)
    {
        $this->cancellingHoldId = $holdId;
        
        try {
            $hold = Hold::where('id', $holdId)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'ready'])
                ->firstOrFail();

            app(LibraryService::class)->cancelHold($hold);
            
            $this->message = "Hold cancelled successfully.";
            $this->messageType = 'success';
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        } finally {
            $this->cancellingHoldId = null;
        }
    }

    public function render()
    {
        $holds = auth()->user()
            ->holds()
            ->with(['book'])
            ->whereIn('status', ['pending', 'ready'])
            ->orderBy('position')
            ->orderBy('requested_date')
            ->get();

        return view('livewire.my-holds.index', [
            'holds' => $holds,
        ])->layout('components.layouts.app.sidebar', [
            'title' => __('My Holds'),
        ]);
    }
}
