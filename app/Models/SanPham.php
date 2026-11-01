<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';

    protected $fillable = [
        'loai_san_pham_id', 'ten', 'mo_ta', 'gia', 'ton_kho',
        'hien_thi', 'anh', 'nhan_vat_id', 'vat_pham_id',
    ];

    protected $casts = [
        'hien_thi' => 'boolean',
    ];

    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class);
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

    // Các mã giảm giá còn hiệu lực của sản phẩm này
    public function maGiamHieuLuc()
    {
        return $this->hasMany(MaGiam::class)->conHieuLuc();
    }

    public function gioHangs()
    {
        return $this->hasMany(GioHang::class);
    }

    public function donHangChiTiets()
    {
        return $this->hasMany(DonHangChiTiet::class);
    }

    // Chỉ lấy sản phẩm đang hiển thị
    public function scopeHienThi($query)
    {
        return $query->where('hien_thi', true);
    }

    // Dòng voucher hiện trên thẻ sản phẩm (rỗng nếu chưa có)
    public function getDongVoucherAttribute(): string
    {
        return $this->maGiamHieuLuc->first()?->mo_ta ?? '';
    }

    public function anhPhu()
{
    return $this->hasMany(AnhSanPham::class)->orderBy('thu_tu');
}

// Đường dẫn ảnh cho thư viện: ảnh chính trước, rồi ảnh phụ, tối đa 6
public function getThuVienAnhAttribute(): array
{
    return collect([$this->anh])
        ->merge($this->anhPhu->pluck('duong_dan'))
        ->filter()
        ->take(6)
        ->values()
        ->all();
}

}