<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = [
        'copy_id',
        'user_id',
        'checkout_date',
        'due_date',
        'returned_date',
        'status',
        'checked_out_by',
        'returned_by',
        'notes',
    ];

    protected $casts = [
        'checkout_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
    ];

    /**
     * Get the copy for this loan
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the user (patron) for this loan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the librarian who checked out the book
     */
    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Get the librarian who returned the book
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Get the fine associated with this loan
     */
    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_date < Carbon::today();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return Carbon::today()->diffInDays($this->due_date);
    }
}
