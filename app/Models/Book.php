<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'published_year',
        'language',
        'pages',
        'category',
        'cover_image',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'pages' => 'integer',
    ];

    /**
     * Get all copies of this book
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    /**
     * Get all loans for this book (through copies)
     */
    public function loans(): HasManyThrough
    {
        return $this->hasManyThrough(Loan::class, Copy::class);
    }

    /**
     * Get all holds for this book
     */
    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    /**
     * Get available copies
     */
    public function availableCopies(): HasMany
    {
        return $this->hasMany(Copy::class)->where('status', 'available');
    }

    /**
     * Check if book has available copies
     */
    public function hasAvailableCopies(): bool
    {
        return $this->availableCopies()->exists();
    }

    /**
     * Get total copies count
     */
    public function getTotalCopiesAttribute(): int
    {
        return $this->copies()->count();
    }

    /**
     * Get available copies count
     */
    public function getAvailableCopiesCountAttribute(): int
    {
        return $this->availableCopies()->count();
    }
}
