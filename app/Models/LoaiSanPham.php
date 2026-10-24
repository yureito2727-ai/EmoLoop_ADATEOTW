<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiSanPham extends Model
{
    protected $table = 'loai_san_pham';

    protected $fillable = ['ten', 'mo_ta'];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class);
    }
}