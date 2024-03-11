<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id()->comment('Mã đánh giá');
            $table->text('content')->nullable(false)->comment('Nội dung');
            $table->string('image')->nullable()->comment('Hình ảnh');
            $table->integer('rating')->nullable(false)->comment('Chất lượng (sao)');
            $table->integer('like_count')->nullable()->comment('Lượt thích');
            $table->foreignIdFor(Review::class)->nullable()->comment('Mã đánh giá chính');
            $table->foreignIdFor(User::class)->comment('Mã người dùng');
            $table->foreignIdFor(Product::class)->unsigned()->comment('Mã sản phẩm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
