<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Shop extends Model
{
    protected $table = 'shops';
    use HasFactory;
    protected $fillable = [
        'name',
        'avatar',
        'email',
        'phone',
        'address',
        'description',
        'rating',
        'status',
        'follower'
    ];

    public function province(): HasMany
    {
        return $this->HasMany(Province::class);
    }
}
