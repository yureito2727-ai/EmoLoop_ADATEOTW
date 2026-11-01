<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anh_san_pham')) {
            return;
        }

        Schema::create('anh_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('san_pham_id')->constrained('san_pham')->cascadeOnDelete();
            $table->string('duong_dan');                    // ví dụ: products/meo-trang-2.jpg
            $table->unsignedInteger('thu_tu')->default(0);  // thứ tự hiển thị trong thư viện
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anh_san_pham');
    }
};
