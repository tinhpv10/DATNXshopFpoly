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
        Schema::create('shop_infos', function (Blueprint $table) {
            $table->id()->comment('Mã thông tin shop');
            $table->foreignIdFor(Shop::class)->comment('Mã nhà bán');
            $table->string('name_bank')->nullable()->comment('Tên ngân hàng');
            $table->string('user_name_bank')->nullable()->comment('Tên tài khoản');
            $table->string('number_bank')->nullable()->comment('Số tài khoản ngân hàng');
            $table->string('profile_number')->nullable(false)->comment('Thông tin');
            $table->string('front_side')->nullable()->comment('Mặt trước chứng minh nhân dân');
            $table->string('back_side')->nullable()->comment('Mặt sau chứng minh nhân dân');
            $table->string('portrait_photo')->nullable()->comment('Hình ảnh khuôn mặt');
            $table->string('national_id')->nullable()->comment('Giấy tùy thân');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_infos');
    }
};
