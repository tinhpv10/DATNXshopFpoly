<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    protected $table = 'wards';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'district_id'
    ];

    public function district(): BelongsTo
    {
        return $this->BelongsTo(District::class);
    }
}
