<?php

namespace App\Support;

class ChonAnh
{
    const NGUONG = 2 / 3;

    public static function chon(array $diem, string $thuMuc): string
    {
        // Thứ tự hy, no, ai, o giúp xử lý hòa điểm (PHP 8 sort ổn định)
        $diem = array_merge(['hy' => 0, 'no' => 0, 'ai' => 0, 'o' => 0], $diem);
        $diem = array_map(fn ($v) => max(0, (int) $v), $diem);

        arsort($diem);
        $ten   = array_keys($diem);
        $chinh = $ten[0];
        $phu   = $ten[1];

        $ketHop = $diem[$phu] > $diem[$chinh] * self::NGUONG;
        $file   = $ketHop ? "{$chinh}_{$phu}.png" : "{$chinh}.png";

        return "characters/{$thuMuc}/{$chinh}/{$file}";
    }
}