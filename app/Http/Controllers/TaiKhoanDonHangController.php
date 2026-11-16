<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;

class TaiKhoanDonHangController extends Controller
{
    public function index(Request $request)
    {
        $loc = $request->query('trang_thai');

        $query = $request->user()->donHangs()
            ->with('chiTiets.sanPham')
            ->withSum('chiTiets as so_san_pham', 'so_luong')
            ->latest('id');

        if ($loc && isset(DonHang::TRANG_THAI[$loc])) {
            $query->where('trang_thai', $loc);
        }

        return view('account.don-hang', [
            'donHangs' => $query->paginate(8)->withQueryString(),
            'loc' => $loc,
        ]);
    }

    public function show(Request $request, DonHang $donHang)
    {
        abort_unless($donHang->user_id === $request->user()->id, 403);

        $donHang->load('chiTiets.sanPham');

        return view('account.don-hang-chi-tiet', [
            'donHang' => $donHang,
            'tamTinh' => $donHang->chiTiets->sum(fn ($c) => $c->gia * $c->so_luong),
        ]);
    }

    public function huy(Request $request, DonHang $donHang)
    {
        abort_unless($donHang->user_id === $request->user()->id, 403);

        $ok = $donHang->huy();

        return redirect()
            ->route('tai-khoan.don-hang.show', $donHang)
            ->with($ok ? 'ok_order' : 'err_order', $ok
                ? 'Đã hủy đơn hàng.'
                : 'Đơn đã được xác nhận nên không thể hủy. Vui lòng liên hệ cửa hàng.');
    }
}
