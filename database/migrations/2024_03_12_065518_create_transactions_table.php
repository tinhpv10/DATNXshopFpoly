<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Order;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->comment('Mã vận chuyển');
            $table->foreignIdFor(User::class)->comment('Mã người dùng');
            $table->foreignIdFor(Order::class)->comment('Mã đơn hàng');
            $table->String('type')->comment('Loại vận chuyển');
            $table->String('status')->comment('Trạng thái');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
