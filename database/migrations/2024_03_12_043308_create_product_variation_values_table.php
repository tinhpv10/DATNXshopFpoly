<?php

use App\Models\AppProductVariation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variation_values', function (Blueprint $table) {
            $table->id()->comment('Mã GTBT');
            $table->foreignIdFor(AppProductVariation::class)->comment('Mã biến thể');
            $table->string('variation_value_name')->nullable()->comment('Tên GTBT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_values');
    }
};
