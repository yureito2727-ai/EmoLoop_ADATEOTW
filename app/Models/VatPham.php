<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatPham extends Model
{
    protected $table = 'vat_pham';

    protected $fillable = ['ten', 'mo_ta', 'anh', 'chuong_id'];

    public function chuong()
    {
        return $this->belongsTo(Chuong::class);
    }

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
        return $this->belongsToMany(User::class, 'nguoi_dung_vat_pham', 'vat_pham_id', 'user_id')
            ->withPivot('ngay_nhan');
    }
}