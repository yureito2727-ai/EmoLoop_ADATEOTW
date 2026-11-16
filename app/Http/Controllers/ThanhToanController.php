<?php

namespace App\Http\Controllers;

use App\Models\DiaChi;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\MaGiam;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ThanhToanController extends Controller
{
    private const PHI_VAN_CHUYEN = 30000;
    private const GIAM_VAN_CHUYEN = 15000;

    public function index(Request $request)
    {
        $user = $request->user();
        $rows = $this->dongDaChon($request, $user->id);

        if ($rows->isEmpty()) {
            return redirect()->route('gio-hang.index');
        }

        return view('thanh-toan.index', [
            'user' => $user,
            'dcLuu' => DiaChi::where('user_id', $user->id)->latest('id')->first(),
            'tinh' => $this->tinh($rows, $user->id),
            'giaoTu' => now()->addDays(3),
            'giaoDen' => now()->addDays(5),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->merge([
            'so_dien_thoai' => preg_replace('/\s+/', '', (string) $request->input('so_dien_thoai')),
        ]);

        $data = $request->validate([
            'chon' => ['nullable', 'array'],
            'chon.*' => ['integer'],
            'ten_nguoi_nhan' => ['required', 'string', 'max:255'],
            'so_dien_thoai' => ['required', 'regex:/^\d{9,11}$/'],
            'dia_chi' => ['required', 'string', 'max:500'],
            'phuong_thuc' => ['required', 'in:cod,the'],
            'the_4_so_cuoi' => ['required_if:phuong_thuc,the', 'nullable', 'digits:4'],
        ], [], [
            'ten_nguoi_nhan' => 'tên người nhận',
            'so_dien_thoai' => 'số điện thoại',
            'dia_chi' => 'địa chỉ nhận hàng',
            'phuong_thuc' => 'phương thức thanh toán',
            'the_4_so_cuoi' => 'số thẻ',
        ]);

        $rows = $this->dongDaChon($request, $user->id);

        if ($rows->isEmpty()) {
            return redirect()->route('gio-hang.index');
        }

        $donHang = DB::transaction(function () use ($rows, $data, $user) {
            $sanPhams = SanPham::whereIn('id', $rows->pluck('san_pham_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($rows as $row) {
                $sp = $sanPhams->get($row->san_pham_id);

                if (! $sp || $sp->ton_kho < $row->so_luong) {
                    throw ValidationException::withMessages([
                        'chon' => 'Sản phẩm "' . ($sp->ten ?? 'không còn bán') . '" không đủ hàng.',
                    ]);
                }
            }

            $tinh = $this->tinh($rows, $user->id);

            $donHang = DonHang::create([
                'user_id' => $user->id,
                'ma_giam_id' => $tinh['ma_chinh'],
                'ten_nguoi_nhan' => $data['ten_nguoi_nhan'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'dia_chi' => $data['dia_chi'],
                'phuong_thuc_thanh_toan' => $data['phuong_thuc'],
                'the_4_so_cuoi' => $data['phuong_thuc'] === 'the' ? $data['the_4_so_cuoi'] : null,
                'trang_thai' => 'cho_xac_nhan',
                'tien_giam' => $tinh['tien_giam'],
                'tong_tien' => $tinh['tong'],
            ]);

            $now = now();

            DB::table('don_hang_chi_tiet')->insert(
                $tinh['dong']->map(fn ($d) => [
                    'don_hang_id' => $donHang->id,
                    'san_pham_id' => $d['san_pham_id'],
                    'so_luong' => $d['so_luong'],
                    'gia' => $d['gia'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );

            foreach ($rows as $row) {
                $sanPhams->get($row->san_pham_id)->decrement('ton_kho', $row->so_luong);
            }

            if ($tinh['ma_ids']) {
                DB::table('nguoi_dung_ma_giam')
                    ->where('user_id', $user->id)
                    ->whereIn('ma_giam_id', $tinh['ma_ids'])
                    ->where('da_su_dung', false)
                    ->update(['da_su_dung' => true, 'ngay_su_dung' => $now]);
            }

            GioHang::whereIn('id', $rows->pluck('id'))->delete();

            return $donHang;
        });

        return redirect()->route('thanh-toan.xong', $donHang);
    }

    public function xong(Request $request, DonHang $donHang)
    {
        abort_unless($donHang->user_id === $request->user()->id, 403);

        return view('thanh-toan.xong', ['donHang' => $donHang]);
    }

    private function dongDaChon(Request $request, int $userId): Collection
    {
        $ids = collect($request->input('chon', []))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        $query = GioHang::with('sanPham')->where('user_id', $userId)->orderBy('id');

        if ($ids->isNotEmpty()) {
            $query->whereIn('id', $ids);
        }

        return $query->get()->filter(fn ($row) => $row->sanPham)->values();
    }

    private function tinh(Collection $rows, int $userId): array
    {
        $maCuaToi = MaGiam::conHieuLuc()
            ->whereIn(
                'id',
                DB::table('nguoi_dung_ma_giam')
                    ->where('user_id', $userId)
                    ->where('da_su_dung', false)
                    ->pluck('ma_giam_id')
            )
            ->whereNotNull('san_pham_id')
            ->get()
            ->groupBy('san_pham_id');

        $maDung = [];
        $maChinh = null;
        $giamMax = 0;

        $dong = $rows->map(function ($row) use ($maCuaToi, &$maDung, &$maChinh, &$giamMax) {
            $gia = (float) $row->sanPham->gia;
            $giamDon = 0;
            $ma = null;

            foreach ($maCuaToi->get($row->san_pham_id, collect()) as $m) {
                $g = in_array($m->loai_giam, ['phan_tram', '%'], true)
                    ? round($gia * (float) $m->gia_tri / 100)
                    : min($gia, (float) $m->gia_tri);

                if ($g > $giamDon) {
                    $giamDon = $g;
                    $ma = $m;
                }
            }

            $giam = $giamDon * $row->so_luong;

            if ($ma) {
                $maDung[] = $ma->id;

                if ($giam > $giamMax) {
                    $giamMax = $giam;
                    $maChinh = $ma->id;
                }
            }

            $thanhTien = $gia * $row->so_luong;

            return [
                'id' => $row->id,
                'san_pham_id' => $row->san_pham_id,
                'ten' => $row->sanPham->ten,
                'anh' => $row->sanPham->anh,
                'url' => route('san-pham.show', $row->san_pham_id),
                'gia' => $gia,
                'so_luong' => $row->so_luong,
                'thanh_tien' => $thanhTien,
                'giam' => $giam,
                'thanh_toan' => $thanhTien - $giam,
                'ma' => $ma ? $ma->ma : null,
                'nhan' => $ma
                    ? (in_array($ma->loai_giam, ['phan_tram', '%'], true)
                        ? '-' . (float) $ma->gia_tri . '%'
                        : '-' . number_format((float) $ma->gia_tri, 0, ',', '.') . 'đ')
                    : null,
            ];
        })->values();

        $hang = $dong->sum('thanh_tien');
        $giamSp = $dong->sum('giam');
        $phi = self::PHI_VAN_CHUYEN;
        $giamShip = min($phi, self::GIAM_VAN_CHUYEN);

        return [
            'dong' => $dong,
            'so_dong' => $dong->count(),
            'hang' => $hang,
            'giam_sp' => $giamSp,
            'hang_sau_giam' => $hang - $giamSp,
            'phi' => $phi,
            'giam_ship' => $giamShip,
            'tien_giam' => $giamSp + $giamShip,
            'tong' => $hang - $giamSp + $phi - $giamShip,
            'ma_ids' => array_values(array_unique($maDung)),
            'ma_chinh' => $maChinh,
        ];
    }
}