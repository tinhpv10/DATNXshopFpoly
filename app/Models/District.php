<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'districts';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'province_id'
    ];

    public function province(): HasMany
    {
        return $this->HasMany(Ward::class);
    }

    public function district(): BelongsTo
    {
        return $this->BelongsTo(Province::class);
    }
}
