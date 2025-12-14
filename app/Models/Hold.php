<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hold extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'requested_date',
        'expiry_date',
        'fulfilled_date',
        'fulfilled_by_copy_id',
        'status',
        'position',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'expiry_date' => 'date',
        'fulfilled_date' => 'date',
    ];

    /**
     * Get the book for this hold
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the user (patron) for this hold
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the copy that fulfilled this hold
     */
    public function fulfilledByCopy(): BelongsTo
    {
        return $this->belongsTo(Copy::class, 'fulfilled_by_copy_id');
    }

    /**
     * Check if hold is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < Carbon::today();
    }

    /**
     * Check if hold is ready (first in queue and copy available)
     */
    public function isReady(): bool
    {
        return $this->status === 'ready' || 
               ($this->status === 'pending' && $this->position === 1 && $this->book->hasAvailableCopies());
    }
}
