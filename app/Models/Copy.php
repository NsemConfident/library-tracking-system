<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Copy extends Model
{
    protected $fillable = [
        'book_id',
        'barcode',
        'status',
        'location',
        'notes',
        'acquired_date',
    ];

    protected $casts = [
        'acquired_date' => 'date',
    ];

    /**
     * Get the book this copy belongs to
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get all loans for this copy
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the active loan for this copy
     */
    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }

    /**
     * Check if copy is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && !$this->activeLoan()->exists();
    }

    /**
     * Check if copy is checked out
     */
    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out' || $this->activeLoan()->exists();
    }
}
