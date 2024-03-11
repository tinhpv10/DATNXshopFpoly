<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\CategoryPost;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CategoryPost::class)->comment('Mã danh mục bài viết');
            $table->string('title')->nullable(false)->comment('Tiêu đề bài viết');
            $table->string('slug')->nullable(false)->comment('Đường dẫn bài viết');
            $table->longText('content')->nullable(false)->comment('Nội dung bài viết');
            $table->string('thumbnail')->nullable(false)->comment('Hình ảnh bài viết');
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->string('meta_keyword')->nullable()->comment('Từ khóa SEO');
            $table->foreignIdFor(User::class)->comment('Mã người đăng bài viết');
            $table->string('tags')->nullable(false)->comment('Nhãn bài viết');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
