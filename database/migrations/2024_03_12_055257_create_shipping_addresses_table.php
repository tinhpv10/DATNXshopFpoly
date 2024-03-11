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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id()->comment('Mã địa chỉ');
            $table->string('name')->nullable(false)->comment('Tên người dùng');
            $table->string('phone')->nullable(false)->comment('Số điện thoại người dùng');
            $table->string('street')->nullable(false)->comment('Địa chỉ cụ thể');
            $table->string('city')->nullable(false)->comment('Tỉnh/Thành phố');
            $table->string('district')->nullable(false)->comment('Huyện/Thị trấn');
            $table->string('ward')->nullable(false)->comment('Xã/Thị xã');
            $table->String('status')->comment('Địa chỉ mặc định');
            $table->foreignIdFor(Order::class)->nullable()->comment('Mã đơn hàng');
            $table->foreignIdFor(User::class)->comment('Mã người dùng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};
