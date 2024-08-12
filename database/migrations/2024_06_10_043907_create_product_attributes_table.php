<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            // Remove the default id primary key and replace it with attribute_id which can be duplicated
            $table->id('attribute_id')->comment('ID ');
            $table->unsignedBigInteger('variation_id')->comment('ID Biến thể');
            $table->foreignIdFor(\App\Models\AppProductVariationValue::class)->nullable(false)->comment('Mã Thuộc tính');
            $table->foreignIdFor(\App\Models\AppProductStock::class)->nullable(false)->comment('sku');
            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
