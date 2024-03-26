<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'id',
        'name',
        'image',
        'category_id',
        'category_slug',
        'status',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'shop_id',
    ];
    use HasFactory;

    protected $casts = [
        'meta_keyword' => 'array',
    ];
    public function shop(): BelongsTo
    {
        return $this->BelongsTo(Shop::class);
    }

    public function categories(): HasMany
    {
        return $this->HasMany(Category::class, 'category_id');
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class, 'category_id');
    }

    public function post(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }

}
