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
        Schema::create('shop_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->comment('Tên log');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('causer_type')->nullable()->comment('Liên kết hoạt động');
            $table->json('properties')->nullable()->comment('Giá trị');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_activity_logs');
    }
};
