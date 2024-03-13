<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Shop;
use App\Models\UserAddress;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Mã người dùng');
            $table->string('email')->unique();
            $table->string('name')->nullable(false)->comment('Tên người dùng');
            $table->date('birthday')->nullable()->comment('Sinh nhật');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Giới tính');
            $table->integer('phone')->nullable()->comment('Số điện thoại người dùng');
            $table->string('password')->nullable(false)->comment('Mật khẩu');
            $table->foreignIdFor(UserAddress::class)->nullable()->comment('Id địa chỉ');
            $table->string('avatar')->nullable()->comment('Hình đại diện');
            $table->foreignIdFor(Shop::class)->nullable()->comment('Mã nhà bán');
            $table->string('verification_code')->nullable()->comment('Mã xác thực');
            $table->string('payment_method')->nullable()->comment('Phương thức thanh toán');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
