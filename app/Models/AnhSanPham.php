<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnhSanPham extends Model
{
    protected $table = 'anh_san_pham';

    protected $fillable = ['san_pham_id', 'duong_dan', 'thu_tu'];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class);
    }
}
