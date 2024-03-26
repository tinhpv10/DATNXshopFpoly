<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasFactory;
    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media',
        'is_main'
    ];
    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
}
