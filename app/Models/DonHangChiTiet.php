<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    protected $table = 'don_hang_chi_tiet';

    protected $fillable = ['don_hang_id', 'san_pham_id', 'so_luong', 'gia'];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class);
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class);
    }
}