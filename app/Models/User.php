<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'vai_tro', 'so_dien_thoai'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gioHangs()
    {
        return $this->hasMany(GioHang::class);
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class);
    }

    public function tienTrinh()
    {
        return $this->hasOne(TienTrinh::class);
    }

    public function maGiams()
    {
        return $this->belongsToMany(MaGiam::class, 'nguoi_dung_ma_giam', 'user_id', 'ma_giam_id')
            ->withPivot('da_su_dung', 'ngay_nhan', 'ngay_su_dung');
    }

    public function vatPhams()
    {
        return $this->belongsToMany(VatPham::class, 'nguoi_dung_vat_pham', 'user_id', 'vat_pham_id')
            ->withPivot('ngay_nhan');
    }

    public function nhanVats()
    {
        return $this->belongsToMany(NhanVat::class, 'nguoi_dung_nhan_vat', 'user_id', 'nhan_vat_id')
            ->withPivot('ngay_chinh_phuc');
    }
}