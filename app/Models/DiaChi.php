<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaChi extends Model
{
    public const QUOC_GIA = [
        'Việt Nam',
        'Hoa Kỳ',
        'Nhật Bản',
        'Hàn Quốc',
        'Trung Quốc',
        'Đài Loan',
        'Singapore',
        'Thái Lan',
        'Malaysia',
        'Úc',
        'Canada',
        'Anh',
        'Pháp',
        'Đức',
    ];

    protected $table = 'dia_chi';

    protected $fillable = [
        'user_id',
        'ten_nguoi_nhan',
        'so_dien_thoai',
        'quoc_gia',
        'tinh_thanh',
        'phuong_xa',
        'dia_chi_cu_the',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}