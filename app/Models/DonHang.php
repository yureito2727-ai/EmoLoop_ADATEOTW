<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'don_hang';

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
}