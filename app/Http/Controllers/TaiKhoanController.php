<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaiKhoanController extends Controller
{
    public function hoSo()
    {
        return view('account.ho-so');
    }

    public function matKhau()
    {
        return view('account.mat-khau');
    }

    public function maGiam(Request $request)
    {
        $ds = $request->user()
            ->maGiams()
            ->with(['sanPham', 'nhanVat', 'vatPham'])
            ->orderByDesc('nguoi_dung_ma_giam.ngay_nhan')
            ->get();

        $conDung = $ds->filter(fn ($m) => ! $m->pivot->da_su_dung
            && $m->dang_hoat_dong
            && (! $m->ngay_het_han || $m->ngay_het_han->isFuture()));

        $daDung = $ds->filter(fn ($m) => $m->pivot->da_su_dung);

        $hetHan = $ds->reject(fn ($m) => $conDung->contains('id', $m->id) || $daDung->contains('id', $m->id));

        return view('account.ma-giam', [
            'conDung' => $conDung->values(),
            'daDung' => $daDung->values(),
            'hetHan' => $hetHan->values(),
        ]);
    }
}
