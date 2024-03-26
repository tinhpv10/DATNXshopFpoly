<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Shop;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supplier::class)->comment('Mã NCC');
            $table->foreignIdFor(Category::class)->comment('Mã danh mục');
            $table->foreignIdFor(Shop::class)->comment('Mã nhà bán');
            $table->string('name')->nullable(false)->comment('Tên sản phẩm');
            $table->string('slug')->nullable()->comment('Đường dẫn sản phẩm ');
            $table->string('regular_price')->nullable(false)->comment('Giá');
            $table->string('sale_price')->nullable(false)->comment('Giá giảm');
            $table->string('sku')->nullable(false)->comment('Mã SKU');
            $table->integer('rating')->nullable()->comment('Đánh giá');
            $table->integer('view_count')->nullable()->comment('Lượt xem');
            $table->integer('sold_count')->nullable()->comment('Lượt bán');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('origin')->nullable()->comment("Nguồn gốc");
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->text('meta_description')->nullable()->comment('Mô tả SEO');
            $table->string('meta_keyword')->nullable()->comment('Từ khóa SEO');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái cửa hàng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
