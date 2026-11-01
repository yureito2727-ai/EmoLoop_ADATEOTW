<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng san_pham: cho phép admin ẩn sản phẩm khỏi shop
        if (! Schema::hasColumn('san_pham', 'hien_thi')) {
            Schema::table('san_pham', function (Blueprint $table) {
                $table->boolean('hien_thi')->default(true)->after('ton_kho');
            });
        }

        // Bảng ma_giam: điều kiện nhận mã, dùng để ghép dòng voucher
        // (ví dụ "Đạt giai đoạn 2" + "giảm 10%")
        if (! Schema::hasColumn('ma_giam', 'dieu_kien')) {
            Schema::table('ma_giam', function (Blueprint $table) {
                $table->string('dieu_kien')->nullable()->after('gia_tri');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('san_pham', 'hien_thi')) {
            Schema::table('san_pham', function (Blueprint $table) {
                $table->dropColumn('hien_thi');
            });
        }

        if (Schema::hasColumn('ma_giam', 'dieu_kien')) {
            Schema::table('ma_giam', function (Blueprint $table) {
                $table->dropColumn('dieu_kien');
            });
        }
    }
};
