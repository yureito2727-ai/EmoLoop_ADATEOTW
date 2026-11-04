<?php

namespace Database\Seeders;

use App\Models\Chuong;
use App\Models\NhanVat;
use Illuminate\Database\Seeder;

class EngelSeeder extends Seeder
{
    public function run(): void
    {
        $engel = NhanVat::updateOrCreate(
            ['thu_muc' => 'engel'],
            ['ten' => 'Engel', 'mo_ta' => 'Bạn cùng lớp (tên tạm)']
        );

        // Nội dung mẫu: 2 giai đoạn, mỗi giai đoạn 1 cột mốc nhỏ + 1 cột mốc lớn
        $noiDung = [
            'nguong_bieu_lo' => ['hy' => 8, 'no' => 8, 'ai' => 8, 'o' => 8],
            'diem_khoi_dau' => ['hy' => 5, 'no' => 0, 'ai' => 0, 'o' => 0],
            'dieu_kien'     => ['hy' => 10],
            'giai_doan'     => [
                [
                    'id' => 1,
                    'cot_moc' => [
                        [
                            'id' => 'g1-nho1', 'loai' => 'nho',
                            'thoai' => [[
                                'nhan_vat' => 'engel',
                                'text' => 'Cậu cũng thấy hôm nay quen quen nhỉ?',
                                'lua_chon' => [
                                    ['text' => 'Ừ, giống hệt hôm qua', 'diem' => ['hy' => 4, 'ai' => 1]],
                                    ['text' => 'Không, chắc cậu nhầm', 'diem' => ['no' => 1, 'o' => 2]],
                                ],
                            ]],
                        ],
                        ['id' => 'g1-lon', 'loai' => 'lon', 'nhan_vat' => 'engel'],
                    ],
                ],
                [
                    'id' => 2,
                    'cot_moc' => [
                        [
                            'id' => 'g2-nho1', 'loai' => 'nho',
                            'thoai' => [[
                                'nhan_vat' => 'engel',
                                'text' => 'Mình có cảm giác đã nói câu này rồi.',
                                'lua_chon' => [
                                    ['text' => 'Kể mình nghe đi', 'diem' => ['hy' => 4, 'ai' => 2]],
                                    ['text' => 'Đừng nghĩ nhiều', 'diem' => ['o' => 2]],
                                ],
                            ]],
                        ],
                        ['id' => 'g2-lon', 'loai' => 'lon', 'nhan_vat' => 'engel'],
                    ],
                ],
            ],
        ];

        $tieuDe = [1 => 'Vòng lặp đầu tiên', 2 => 'Ngày giống hệt hôm qua'];

        foreach ($tieuDe as $thuTu => $ten) {
            Chuong::updateOrCreate(
                ['nhan_vat_id' => $engel->id, 'thu_tu' => $thuTu],
                ['tieu_de' => $ten, 'noi_dung' => $noiDung, 'da_xuat_ban' => true]
            );
        }
    }
}