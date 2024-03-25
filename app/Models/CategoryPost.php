<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryPost extends Model
{
    protected $table = 'category_posts';
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function post(): HasMany
    {
        return $this->HasMany(Post::class);
    }
}
