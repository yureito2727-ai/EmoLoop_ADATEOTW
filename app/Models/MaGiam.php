<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaGiam extends Model
{
    protected $table = 'ma_giam';

    protected $fillable = ['ma', 'loai_giam', 'gia_tri', 'ngay_het_han', 'dang_hoat_dong', 'san_pham_id', 'nhan_vat_id', 'vat_pham_id', 'dieu_kien'];

    protected $casts = [
        'ngay_het_han' => 'datetime',
        'dang_hoat_dong' => 'boolean',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class);
    }

    public function nhanVat()
    {
        return $this->belongsTo(NhanVat::class);
    }

    public function vatPham()
    {
        return $this->belongsTo(VatPham::class);
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class);
    }

    public function nguoiDung()
    {
        return $this->belongsToMany(User::class, 'nguoi_dung_ma_giam', 'ma_giam_id', 'user_id')
            ->withPivot('da_su_dung', 'ngay_nhan', 'ngay_su_dung');
    }

    // Mã còn bật và chưa hết hạn
    public function scopeConHieuLuc($query)
    {
        return $query->where('dang_hoat_dong', true)
            ->where(function ($q) {
                $q->whereNull('ngay_het_han')
                  ->orWhere('ngay_het_han', '>', now());
            });
    }

    // Ví dụ: "Đạt giai đoạn 2: giảm 10%"
    public function getMoTaAttribute(): string
    {
        $giam = $this->loai_giam === '%'
            ? 'giảm ' . (float) $this->gia_tri . '%'
            : 'giảm ' . number_format($this->gia_tri, 0, ',', '.') . 'đ';

        return $this->dieu_kien ? $this->dieu_kien . ': ' . $giam : ucfirst($giam);
    }
}