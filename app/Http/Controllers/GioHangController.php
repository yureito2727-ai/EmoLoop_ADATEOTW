<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\GioHang;
use App\Models\MaGiam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GioHangController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $maCuaToi = MaGiam::whereIn(
            'id',
            DB::table('nguoi_dung_ma_giam')
                ->where('user_id', $userId)
                ->where('da_su_dung', false)
                ->pluck('ma_giam_id')
        )
            ->where('dang_hoat_dong', true)
            ->whereNotNull('san_pham_id')
            ->where(function ($q) {
                $q->whereNull('ngay_het_han')->orWhere('ngay_het_han', '>', now());
            })
            ->get()
            ->groupBy('san_pham_id');

        $items = GioHang::with('sanPham')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get()
            ->filter(fn ($row) => $row->sanPham)
            ->map(function ($row) use ($maCuaToi) {
                $gia = (float) $row->sanPham->gia;
                $giam = 0;
                $ma = null;

                foreach ($maCuaToi->get($row->san_pham_id, collect()) as $m) {
                    $g = $m->loai_giam === 'phan_tram'
                        ? $gia * (float) $m->gia_tri / 100
                        : min($gia, (float) $m->gia_tri);
                    if ($g > $giam) {
                        $giam = $g;
                        $ma = $m;
                    }
                }

                return [
                    'id' => $row->id,
                    'ten' => $row->sanPham->ten,
                    'anh' => $row->sanPham->anh,
                    'gia' => $gia,
                    'so_luong' => $row->so_luong,
                    'giam' => $giam,
                    'ma' => $ma ? $ma->ma : null,
                    'nhan' => $ma
                        ? ($ma->loai_giam === 'phan_tram' ? '-' . rtrim(rtrim($ma->gia_tri, '0'), '.') . '%' : '-' . number_format($ma->gia_tri, 0, ',', '.') . 'đ')
                        : null,
                    'url' => route('san-pham.show', $row->san_pham_id),
                ];
            })
            ->values();

        return view('gio-hang.index', ['items' => $items]);
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'san_pham_id' => ['required', 'integer', 'exists:san_pham,id'],
        'so_luong' => ['nullable', 'integer', 'min:1', 'max:99'],
    ]);

    $sanPham = SanPham::findOrFail($data['san_pham_id']);

    if ($sanPham->ton_kho < 1) {
        return response()->json(['ok' => false, 'message' => 'Sản phẩm đã hết hàng.'], 422);
    }

    $row = GioHang::firstOrNew([
        'user_id' => $request->user()->id,
        'san_pham_id' => $sanPham->id,
    ]);

    $row->so_luong = min(99, $sanPham->ton_kho, ($row->so_luong ?? 0) + ($data['so_luong'] ?? 1));
    $row->save();

    $tong = GioHang::where('user_id', $request->user()->id)->count();

    if ($request->expectsJson()) {
        return response()->json(['ok' => true, 'tong' => $tong, 'url' => route('gio-hang.index')]);
    }

    return redirect()->route('gio-hang.index')->with('status', 'Đã thêm vào giỏ hàng.');
}

    public function update(Request $request, GioHang $gioHang)
    {
        abort_unless($gioHang->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'so_luong' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $gioHang->so_luong = $data['so_luong'];
        $gioHang->save();

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, GioHang $gioHang)
    {
        abort_unless($gioHang->user_id === $request->user()->id, 403);
        $gioHang->delete();

        return response()->json(['ok' => true]);
    }

    public function clear(Request $request)
    {
        GioHang::where('user_id', $request->user()->id)->delete();

        return response()->json(['ok' => true]);
    }
}