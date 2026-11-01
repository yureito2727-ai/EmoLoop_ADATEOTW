<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dia_chi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('ten_nguoi_nhan')->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('quoc_gia', 100)->default('Việt Nam');
            $table->string('tinh_thanh', 150);
            $table->string('phuong_xa', 150)->nullable();
            $table->string('dia_chi_cu_the', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dia_chi');
    }
};