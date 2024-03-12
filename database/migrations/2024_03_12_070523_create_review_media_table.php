<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Review;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('review_media', function (Blueprint $table) {
            $table->id()->comment('Mã đánh giá có hình ảnh');
            $table->foreignIdFor(Review::class)->comment('Mã đánh giá');
            $table->string('review_media')->nullable()->comment('Hình ảnh đánh giá');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riview_media');
    }
};
