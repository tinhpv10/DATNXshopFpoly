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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('canceled_by')->nullable()->comment('Huỷ bới ai');
            $table->tinyInteger('check_order_shop')->default(0)->nullable()->comment('shop chuẩn bị hàng mặc định là chưa(0)');
            $table->string('lading_code')->nullable()->comment('Mã vận đơn');
            $table->date('custom_time')->nullable()->comment('Thời gian shop chưa xử lý đơn hàng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('canceled_by');
        });
    }
};
