<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TienTrinh extends Model
{
    protected $table = 'tien_trinh';

    protected $fillable = [
        'user_id', 'chuong_id', 'giai_doan', 'cot_moc',
        'diem_hy', 'diem_no', 'diem_ai', 'diem_o', 'anh_hien_tai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chuong()
    {
        return $this->belongsTo(Chuong::class);
    }
}