<?php

namespace App\Http\Controllers;

use App\Models\Chuong;
use App\Models\NhanVat;
use App\Models\TienTrinh;

class GameController extends Controller
{
    public function chonNhanVat()
    {
        $nhanVats = NhanVat::orderBy('id')->get();

        return view('game.chon-nhan-vat', compact('nhanVats'));
    }

    public function danhSachChuong(NhanVat $nhanVat)
    {
        $chuongs = $nhanVat->chuongs()->where('da_xuat_ban', true)->get();

        $tienTrinh = TienTrinh::where('user_id', auth()->id())
            ->where('nhan_vat_id', $nhanVat->id)
            ->first();

        if ($tienTrinh && $chuongDangMo = Chuong::find($tienTrinh->chuong_id)) {
            $xaNhat = $chuongDangMo->thu_tu;
        } else {
            $xaNhat = $chuongs->first()->thu_tu ?? 1;
        }

        return view('game.chuong-ds', compact('nhanVat', 'chuongs', 'xaNhat'));
    }
}