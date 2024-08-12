<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $table = 'reviews';
    use HasFactory;

    protected $fillable = [
        'content',
        'image',
        'rating',
        'like_count',
        'review_id',
        'user_id',
        'product_id',
        'processing'
    ];

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    public function review(): HasMany
    {
        return $this->HasMany(Review::class);
    }

    public function reviewChild(): BelongsTo
    {
        return $this->BelongsTo(Review::class);
    }

    public function like(): HasMany
    {
        return $this->HasMany(Like::class);
    }

    public function reviewMedia(): HasMany
    {
        return $this->HasMany(ReviewMedia::class);
    }
}
