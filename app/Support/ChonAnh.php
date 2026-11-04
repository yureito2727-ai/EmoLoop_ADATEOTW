<?php

namespace App\Support;

class ChonAnh
{
    /** Cảm xúc phụ phải lớn hơn điểm cao nhất × 2/3 mới ra ảnh kết hợp. */
    public const NGUONG = 2 / 3;

    /** Thư mục và tên ảnh của cảm xúc "bình thường" (mặt điềm tĩnh), không có điểm. */
    public const BINH_THUONG = 'bt';

    /** Ngưỡng biểu lộ mặc định (giá trị mẫu). Chương nào cần khác thì ghi đè bằng noi_dung.nguong_bieu_lo. */
    public const NGUONG_BIEU_LO = ['hy' => 17, 'no' => 29, 'ai' => 23, 'o' => 30];

    private const THU_TU = ['hy', 'no', 'ai', 'o']; // cũng là thứ tự ưu tiên khi hòa điểm

    /** Ngưỡng biểu lộ của một chương: mặc định, có thể ghi đè từng cảm xúc trong noi_dung. */
    public static function nguong(array $noiDung): array
    {
        $ghiDe = $noiDung['nguong_bieu_lo'] ?? [];
        $kq = [];
        foreach (self::NGUONG_BIEU_LO as $k => $macDinh) {
            $kq[$k] = isset($ghiDe[$k]) ? max(0, (int) $ghiDe[$k]) : $macDinh;
        }

        return $kq;
    }

    public static function chon(array $diem, string $thuMuc, ?array $nguong = null): string
    {
        $nguong ??= self::NGUONG_BIEU_LO;

        // Chỉ những cảm xúc đã đạt ngưỡng biểu lộ của chính nó mới được xét
        $du = [];
        foreach (self::THU_TU as $k) {
            $v = max(0, (int) ($diem[$k] ?? 0));
            if ($v > 0 && $v >= ($nguong[$k] ?? self::NGUONG_BIEU_LO[$k])) {
                $du[$k] = $v;
            }
        }

        // Chưa cảm xúc nào đạt ngưỡng: giữ mặt điềm tĩnh
        if (! $du) {
            return "characters/{$thuMuc}/" . self::BINH_THUONG . '/' . self::BINH_THUONG . '.png';
        }

        arsort($du); // PHP 8 sắp xếp ổn định: hòa điểm thì giữ thứ tự hỷ, nộ, ái, ố
        $ten   = array_keys($du);
        $chinh = $ten[0];
        $phu   = $ten[1] ?? null; // cảm xúc phụ cũng phải đạt ngưỡng của nó

        $ketHop = $phu !== null && $du[$phu] > $du[$chinh] * self::NGUONG;
        $file   = $ketHop ? "{$chinh}_{$phu}.png" : "{$chinh}.png";

        return "characters/{$thuMuc}/{$chinh}/{$file}";
    }
}