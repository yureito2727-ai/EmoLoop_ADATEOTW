<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVat extends Model
{
    protected $table = 'nhan_vat';

    protected $fillable = ['ten', 'thu_muc', 'mo_ta', 'anh'];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class);
    }

    public function maGiams()
    {
        return $this->hasMany(MaGiam::class);
    }

    public function nguoiChoi()
    {
        return $this->belongsToMany(User::class, 'nguoi_dung_nhan_vat', 'nhan_vat_id', 'user_id')
            ->withPivot('ngay_chinh_phuc');
    }
}