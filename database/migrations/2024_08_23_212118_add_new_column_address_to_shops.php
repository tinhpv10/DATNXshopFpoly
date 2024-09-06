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
        Schema::table('shops', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Province::class)->nullable()->comment('Mã tỉnh');
            $table->foreignIdFor(\App\Models\District::class)->nullable()->comment('Mã huyện');
            $table->foreignIdFor(\App\Models\Ward::class)->nullable()->comment('Mã xã');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            //
        });
    }
};
