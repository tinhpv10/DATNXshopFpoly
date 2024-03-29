<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $table = 'provinces';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
    ];

    public function province(): HasMany
    {
        return $this->HasMany(District::class);
    }
}
