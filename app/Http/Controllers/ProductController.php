<?php

namespace App\Http\Controllers;

use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Trang shop: /shop?loai=1&q=meo&voucher=1&sort=asc
    public function index(Request $request)
    {
        $loais = LoaiSanPham::orderBy('id')->get();

        // Mặc định chọn loại đầu tiên, giống mockup
        $loaiId = $request->integer('loai') ?: $loais->first()?->id;

        $tuKhoa = trim((string) $request->query('q', ''));
        $coVoucher = $request->boolean('voucher');
        $sapXep = $request->query('sort');

        $query = SanPham::hienThi()
            ->with('maGiamHieuLuc')
            ->where('loai_san_pham_id', $loaiId);

        if ($tuKhoa !== '') {
            $query->where('ten', 'like', '%' . $tuKhoa . '%');
        }

        if ($coVoucher) {
            $query->whereHas('maGiamHieuLuc');
        }

        match ($sapXep) {
            'asc' => $query->orderBy('gia'),
            'desc' => $query->orderBy('gia', 'desc'),
            default => $query->orderBy('id'),
        };

        $sanPhams = $query->get();

        return view('shop.index', [
            'loais' => $loais,
            'loaiId' => $loaiId,
            'sanPhams' => $sanPhams,
            'tuKhoa' => $tuKhoa,
            'coVoucher' => $coVoucher,
            'sapXep' => $sapXep,
        ]);
    }

    // Trang chi tiết: /san-pham/{id}
public function show(SanPham $sanPham)
{
    // Sản phẩm bị ẩn thì coi như không tồn tại
    abort_unless($sanPham->hien_thi, 404);

    $sanPham->load('anhPhu');

    return view('shop.show', [
        'sp' => $sanPham,
        'anh' => $sanPham->thu_vien_anh,
    ]);
} 

}