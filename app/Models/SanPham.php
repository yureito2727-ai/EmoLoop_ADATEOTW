<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';

    protected $fillable = ['loai_san_pham_id', 'ten', 'mo_ta', 'gia', 'ton_kho', 'anh', 'nhan_vat_id', 'vat_pham_id'];

    public function loai()
    {
        return $this->belongsTo(LoaiSanPham::class, 'loai_san_pham_id');
    }

    public function nhanVat()
    {
        return $this->belongsTo(NhanVat::class);
    }

    public function vatPham()
    {
        return $this->belongsTo(VatPham::class);
    }

    public function maGiams()
    {
        return $this->hasMany(MaGiam::class);
    }
}