<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DonHang extends Model
{
    protected $table = 'don_hang';
        public const TRANG_THAI = [
        'cho_xac_nhan'  => 'Chờ xác nhận',
        'dang_chuan_bi' => 'Đang chuẩn bị',
        'dang_giao'     => 'Đang giao',
        'da_giao'       => 'Đã giao',
        'da_huy'        => 'Đã hủy',
    ];

    // Các bước hiện trên thanh theo dõi (không gồm đã hủy)
    public const BUOC_THEO_DOI = ['cho_xac_nhan', 'dang_chuan_bi', 'dang_giao', 'da_giao'];

    protected $fillable = [
        'user_id', 'ma_giam_id', 'ten_nguoi_nhan', 'so_dien_thoai', 'dia_chi',
        'phuong_thuc_thanh_toan', 'the_4_so_cuoi', 'trang_thai', 'tien_giam', 'tong_tien',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maGiam()
    {
        return $this->belongsTo(MaGiam::class);
    }

    public function chiTiets()
    {
        return $this->hasMany(DonHangChiTiet::class);
    }

        public function getNhanTrangThaiAttribute(): string
    {
        return self::TRANG_THAI[$this->trang_thai] ?? $this->trang_thai;
    }

    public function coTheHuy(): bool
    {
        return $this->trang_thai === 'cho_xac_nhan';
    }

    // Hủy đơn: trả tồn kho, trả mã giảm, đổi trạng thái. Trả về false nếu đơn không còn hủy được.
    // Cả trang khách lẫn trang quản trị sau này đều gọi hàm này.
    public function huy(): bool
    {
        return DB::transaction(function () {
            $don = static::whereKey($this->id)->lockForUpdate()->first();

            if (! $don->coTheHuy()) {
                return false;
            }

            foreach ($don->chiTiets as $ct) {
                SanPham::where('id', $ct->san_pham_id)->increment('ton_kho', $ct->so_luong);
            }

            if ($don->ma_giam_id) {
                DB::table('nguoi_dung_ma_giam')
                    ->where('user_id', $don->user_id)
                    ->where('ma_giam_id', $don->ma_giam_id)
                    ->update(['da_su_dung' => false, 'ngay_su_dung' => null]);
            }

            $don->trang_thai = 'da_huy';
            $don->save();
            $this->trang_thai = 'da_huy';

            return true;
        });
    }
}