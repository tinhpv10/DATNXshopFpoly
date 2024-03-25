<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $table = 'posts';
    use HasFactory;

    protected $fillable = [
        'category_post_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'meta_title',
        'meta_keyword',
        'user_id',
        'tags',
    ];
    protected $casts = [
        'tags' => 'array',
        'meta_keyword' => 'array',
    ];

    public function categoryPost(): BelongsTo
    {
        return $this->BelongsTo(CategoryPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
