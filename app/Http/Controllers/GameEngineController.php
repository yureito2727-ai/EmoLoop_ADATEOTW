<?php

namespace App\Http\Controllers;

use App\Models\Chuong;
use App\Models\TienTrinh;
use App\Support\ChonAnh;
use Illuminate\Http\Request;

class GameEngineController extends Controller
{
    public function vaoChuong(Request $request, Chuong $chuong)
    {
        $user = $request->user();

        abort_unless($chuong->da_xuat_ban, 404);

        $tt = TienTrinh::where('user_id', $user->id)
            ->where('nhan_vat_id', $chuong->nhan_vat_id)
            ->first();

        // Chương xa nhất đã mở; chưa có tiến trình thì chỉ mở chương 1
        $thuTuDaMo = $tt ? $tt->chuong->thu_tu : 1;
        abort_if($chuong->thu_tu > $thuTuDaMo, 403, 'Chương này đang bị khóa.');

        $noiDung = $chuong->noi_dung;
        $diem = array_merge(
            ['hy' => 0, 'no' => 0, 'ai' => 0, 'o' => 0],
            $noiDung['diem_khoi_dau'] ?? []
        );
        $diem = array_map(fn ($v) => max(0, (int) $v), $diem);

        $anh = ChonAnh::chon($diem, $chuong->nhanVat->thu_muc);
        $cotMocDau = $noiDung['giai_doan'][0]['cot_moc'][0]['id'] ?? '';

        if (! $tt) {
            $tt = new TienTrinh([
                'user_id'     => $user->id,
                'nhan_vat_id' => $chuong->nhan_vat_id,
                'chuong_id'   => $chuong->id,
            ]);
        }
        // Chơi lại chương cũ: KHÔNG hạ chuong_id (chương xa nhất đã mở)

        $tt->fill([
            'diem_hy'      => $diem['hy'],
            'diem_no'      => $diem['no'],
            'diem_ai'      => $diem['ai'],
            'diem_o'       => $diem['o'],
            'giai_doan'    => 1,
            'cot_moc'      => $cotMocDau,
            'anh_hien_tai' => $anh,
        ])->save();

        return response()->json([
            'chuong' => [
                'id'      => $chuong->id,
                'tieu_de' => $chuong->tieu_de,
                'thu_tu'  => $chuong->thu_tu,
            ],
            'thu_muc'  => $chuong->nhanVat->thu_muc,
            'diem'     => $diem,
            'anh'      => $anh,
            'noi_dung' => $noiDung,
        ]);
    }

    public function trangChoi(Chuong $chuong)
{
    abort_unless($chuong->da_xuat_ban, 404);

    return view('game.chuong', compact('chuong'));
}
}