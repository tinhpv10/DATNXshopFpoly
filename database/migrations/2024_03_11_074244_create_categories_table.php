<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Shop;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('Tên danh mục');
            $table->string('image')->nullable()->comment('Hình ảnh');
            $table->string('category_id')->nullable(false)->comment('Id danh mục');
            $table->string('category_slug')->nullable()->comment('Slug danh mục');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái');
            $table->foreignIdFor(Shop::class)->comment('Mã nhà bán');
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->text('meta_description')->nullable()->comment('Mô tả SEO');
            $table->string('meta_keyword')->nullable()->comment("Từ khóa SEO");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');

    }
};
