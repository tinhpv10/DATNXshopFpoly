<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppProductMedia extends Model
{
    use HasFactory;
    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media',
        'name_media',
        'is_main',
    ];

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }

    public function productStock(): BelongsTo
    {
        return $this->BelongsTo(AppProductStock::class);
    }
}
