<?php

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
        Schema::create('brands', function (Blueprint $table) {
            $table->id()->comment('Mã');
            $table->string('name')->nullable(false)->comment('Tên');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('image')->nullable()->comment('Hình ảnh');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
