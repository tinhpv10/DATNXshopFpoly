<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\OrderStatus;
use App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('Mã thông báo');
            $table->string('title')->nullable(false)->comment('Tiêu đề');
            $table->string('content')->nullable(false)->comment('Nội dung');
            $table->foreignIdFor(OrderStatus::class)->comment('Mã trạng thái');
            $table->text('type')->nullable()->comment('Loại thông báo');
            $table->foreignIdFor(User::class)->comment('Mã người dùng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
