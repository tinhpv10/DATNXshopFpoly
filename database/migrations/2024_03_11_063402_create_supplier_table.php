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
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('Tên NCC');
            $table->string('email')->nullable(false)->comment('Email NCC');
            $table->integer('phone')->nullable(false)->comment('SĐT NCC');
            $table->string('address')->nullable(false)->comment('Địa chỉ NCC');
            $table->string('website')->nullable()->comment('Website của NCC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
