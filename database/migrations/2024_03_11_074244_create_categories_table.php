<?php

use App\Models\Category;
use App\Models\Shop;
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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('Tên danh mục');
            $table->string('image')->nullable()->comment('Hình ảnh');
            $table->string('category_slug')->unique()->nullable(false)->comment('Slug danh mục');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái');
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->text('meta_description')->nullable()->comment('Mô tả SEO');
            $table->string('meta_keyword')->nullable()->comment("Từ khóa SEO");
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Id danh mục cha');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
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
