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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false) ->comment('Tên vai trò');
            $table->string('guard_name')->nullable(false) ->comment('Tên bảo vệ');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->foreignIdFor(Shop::class)->comment('Mã cửa hàng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
