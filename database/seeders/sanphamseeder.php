<?php

namespace Database\Seeders;

use App\Models\LoaiSanPham;
use App\Models\MaGiam;
use App\Models\SanPham;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SanPhamSeeder extends Seeder
{
    public function run(): void
    {
        // unguarded: không phụ thuộc $fillable của từng model
        Model::unguarded(function () {
            // 1. Loại sản phẩm
            $moc = LoaiSanPham::updateOrCreate(
                ['ten' => 'Móc khóa'],
                ['mo_ta' => 'Móc khóa từ vật phẩm trong game']
            );
            $item = LoaiSanPham::updateOrCreate(
                ['ten' => 'Item trưng bày'],
                ['mo_ta' => 'Đồ trang trí để bàn']
            );
            $mohinh = LoaiSanPham::updateOrCreate(
                ['ten' => 'Mô hình'],
                ['mo_ta' => 'Tượng nhân vật']
            );

            // 2. Sản phẩm: [loại, tên, giá, tồn kho]
            $sanPhams = [
                [$moc, 'Móc khóa Mèo Trắng', 49000, 20],
                [$moc, 'Móc khóa Đồng Hồ Cát', 59000, 12],
                [$moc, 'Móc khóa Chiếc Chìa Khóa', 45000, 35],
                [$moc, 'Móc khóa Bông Hoa Héo', 55000, 8],
                [$moc, 'Móc khóa Cánh Bướm', 49000, 25],
                [$moc, 'Móc khóa Mặt Trăng Khuyết', 65000, 15],
                [$moc, 'Móc khóa Chiếc Ô Đỏ', 59000, 30],
                [$moc, 'Móc khóa Con Rối', 79000, 5],
                [$moc, 'Móc khóa Cuốn Nhật Ký', 69000, 18],
                [$moc, 'Móc khóa Ngôi Sao Vỡ', 55000, 22],
                [$moc, 'Móc khóa Giọt Lệ', 49000, 40],
                [$moc, 'Móc khóa Vòng Lặp', 89000, 10],
                // Thêm vài món cho 2 loại còn lại để bấm tab không bị trống
                [$item, 'Khung Mặt Trăng', 120000, 15],
                [$item, 'Đèn Đồng Hồ Cát', 189000, 6],
                [$mohinh, 'Mô hình Hoa Héo', 259000, 6],
                [$mohinh, 'Mô hình Con Rối', 289000, 0],
            ];

            $sp = [];
            foreach ($sanPhams as [$loai, $ten, $gia, $ton]) {
                $sp[$ten] = SanPham::updateOrCreate(
                    ['ten' => $ten],
                    [
                        'loai_san_pham_id' => $loai->id,
                        'gia' => $gia,
                        'ton_kho' => $ton,
                        'hien_thi' => true,
                    ]
                );
            }

            // 3. Mã giảm giá: [mã, sản phẩm, điều kiện nhận, loại, giá trị]
            $maGiams = [
                ['MEO10', 'Móc khóa Mèo Trắng', 'Đạt giai đoạn 2', '%', 10],
                ['DONGHO15', 'Móc khóa Đồng Hồ Cát', 'Nhặt vật phẩm Đồng hồ cát', '%', 15],
                ['HOA20', 'Móc khóa Bông Hoa Héo', 'Chinh phục nhân vật Hoa', '%', 20],
                ['TRANG10', 'Móc khóa Mặt Trăng Khuyết', 'Đạt giai đoạn 3', '%', 10],
                ['ROI20', 'Móc khóa Con Rối', 'Chinh phục nhân vật Rối', '%', 20],
                ['NHATKY15', 'Móc khóa Cuốn Nhật Ký', 'Nhặt vật phẩm Nhật ký', '%', 15],
                ['LE05', 'Móc khóa Giọt Lệ', 'Đạt giai đoạn 1', '%', 5],
            ];

            foreach ($maGiams as [$ma, $tenSp, $dieuKien, $loaiGiam, $giaTri]) {
                MaGiam::updateOrCreate(
                    ['ma' => $ma],
                    [
                        'loai_giam' => $loaiGiam,
                        'gia_tri' => $giaTri,
                        'dieu_kien' => $dieuKien,
                        'san_pham_id' => $sp[$tenSp]->id,
                        'ngay_het_han' => now()->addYear()->endOfDay(),
                        'dang_hoat_dong' => true,
                    ]
                );
            }
        });
    }
}