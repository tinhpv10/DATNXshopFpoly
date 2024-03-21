<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    use HasFactory;

    protected $fillable = [
        'method_name',
    ];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
