<?php

namespace App\Http\Controllers;

use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Http\Request;
use App\Models\AnhSanPham;
use Illuminate\Support\Facades\Storage;

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
            ->with(['maGiamHieuLuc', 'anhPhu'])
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
    abort_unless($sanPham->hien_thi, 404);

    $sanPham->load('anhPhu');

    $goiY = SanPham::hienThi()
        ->where('ton_kho', '>', 0)
        ->where('id', '!=', $sanPham->id)
        ->inRandomOrder()
        ->limit(4)
        ->get();

    return view('shop.show', [
        'sp' => $sanPham,
        'anh' => $sanPham->thu_vien_anh,
        'goiY' => $goiY,
    ]);
}

    public function update(Request $request, SanPham $sanPham)
    {
        abort_unless($request->user()->laQuanTri(), 403);

        $data = $request->validate([
            'ten' => ['required', 'string', 'max:255'],
            'loai_san_pham_id' => ['required', 'exists:loai_san_pham,id'],
            'gia' => ['required', 'numeric', 'min:0'],
            'mo_ta' => ['nullable', 'string'],
            'anh' => ['nullable', 'image', 'max:4096'],
            'anh_phu' => ['nullable', 'array'],
            'anh_phu.*' => ['image', 'max:4096'],
            'xoa_anh_phu' => ['nullable', 'array'],
            'xoa_anh_phu.*' => ['integer'],
        ], [], [
            'ten' => 'tên sản phẩm',
            'loai_san_pham_id' => 'loại sản phẩm',
            'gia' => 'giá',
            'mo_ta' => 'mô tả',
            'anh' => 'ảnh đại diện',
            'anh_phu.*' => 'ảnh phụ',
        ]);

        $xoa = $data['xoa_anh_phu'] ?? [];
        $moi = $request->file('anh_phu', []);

        $conLai = AnhSanPham::where('san_pham_id', $sanPham->id)
            ->whereNotIn('id', $xoa)
            ->count();

        if ($conLai + count($moi) > 5) {
            return back()->withInput()->withErrors(['anh_phu' => 'Ảnh phụ tối đa 5 ảnh.']);
        }

        $sanPham->fill([
            'ten' => $data['ten'],
            'loai_san_pham_id' => $data['loai_san_pham_id'],
            'gia' => $data['gia'],
            'mo_ta' => $data['mo_ta'] ?? null,
        ]);

        if ($request->hasFile('anh')) {
            if ($sanPham->anh) {
                Storage::disk('public')->delete($sanPham->anh);
            }
            $sanPham->anh = $request->file('anh')->store('san-pham', 'public');
        }

        $sanPham->save();

        foreach (AnhSanPham::where('san_pham_id', $sanPham->id)->whereIn('id', $xoa)->get() as $ap) {
            Storage::disk('public')->delete($ap->duong_dan);
            $ap->delete();
        }

        $thuTu = (int) AnhSanPham::where('san_pham_id', $sanPham->id)->max('thu_tu');

        foreach ($moi as $file) {
            $thuTu++;
            AnhSanPham::create([
                'san_pham_id' => $sanPham->id,
                'duong_dan' => $file->store('san-pham', 'public'),
                'thu_tu' => $thuTu,
            ]);
        }

        return back()->with('status', 'Đã cập nhật sản phẩm.');
    }

}