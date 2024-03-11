<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\VoucherType;
use App\Models\Shop;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id()->comment('Mã voucher');
            $table->string('name')->nullable(false)->comment('Tên voucher');
            $table->string('code')->nullable(false)->comment('Mã giảm');
            $table->text('description')->nullable(false)->comment('Mô tả');
            $table->integer('usage_limit')->nullable(false)->comment('Số lần dùng');
            $table->double('max_discount_amount')->nullable(false)->comment('Giảm tối đa');
            $table->double('discount')->nullable(false)->comment('Giảm giá');
            $table->date('start_date')->nullable(false)->comment('Ngày bắt đầu');
            $table->date('expiration')->nullable(false)->comment('Ngày hết hạn');
            $table->foreignIdFor(Shop::class)->comment('Mã nhà bán');
            $table->foreignIdFor(VoucherType::class)->comment('Mã loại voucher');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
