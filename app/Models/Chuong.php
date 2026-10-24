<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chuong extends Model
{
    protected $table = 'chuong';

    protected $fillable = ['tieu_de', 'thu_tu', 'noi_dung', 'da_xuat_ban'];

    protected $casts = [
        'noi_dung' => 'array',
        'da_xuat_ban' => 'boolean',
    ];

    public function vatPhams()
    {
        return $this->hasMany(VatPham::class);
    }

    public function tienTrinhs()
    {
        return $this->hasMany(TienTrinh::class);
    }
}