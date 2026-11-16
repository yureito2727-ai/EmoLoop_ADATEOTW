@extends('account.layout')

@section('title', 'Đơn #'.$donHang->id.' – Emo Loop')
@section('panel_title', 'Đơn hàng #'.$donHang->id)

@section('panel')
<p><a href="{{ route('tai-khoan.don-hang') }}" class="acct-back"><i class="bi bi-arrow-left"></i> Danh sách đơn hàng</a></p>

@if (session('ok_order'))<div class="acc-ok">{{ session('ok_order') }}</div>@endif
@if (session('err_order'))<div class="acct-err">{{ session('err_order') }}</div>@endif

<div class="acct-card">
    @if ($donHang->trang_thai === 'da_huy')
        <div class="acct-cancelled"><i class="bi bi-x-circle"></i> Đơn hàng đã bị hủy</div>
    @else
        @php($vt = array_search($donHang->trang_thai, \App\Models\DonHang::BUOC_THEO_DOI, true))
        <ol class="acct-track">
            @foreach (\App\Models\DonHang::BUOC_THEO_DOI as $i => $b)
                <li @class(['done' => $vt !== false && $i < $vt, 'now' => $i === $vt])>
                    <span class="dot">@if ($vt !== false && $i < $vt)<i class="bi bi-check-lg"></i>@endif</span>
                    <span class="lbl">{{ \App\Models\DonHang::TRANG_THAI[$b] }}</span>
                </li>
            @endforeach
        </ol>
    @endif
    <small class="acct-note">Đặt lúc {{ $donHang->created_at?->format('H:i d/m/Y') }}</small>
</div>

<div class="acct-card">
    <h2 class="acct-sub mt-0">Sản phẩm</h2>
    @foreach ($donHang->chiTiets as $ct)
        @php($sp = $ct->sanPham)
        <div class="acct-line">
            @if ($sp?->anh)
                <img src="{{ asset('storage/'.$sp->anh) }}" alt="" onerror="this.style.display='none'">
            @endif
            <div class="acct-line-name">
                @if ($sp)
                    <a href="{{ route('san-pham.show', $sp->id) }}">{{ $sp->ten }}</a>
                @else
                    <span>Sản phẩm không còn bán</span>
                @endif
                <small>{{ number_format($ct->gia, 0, ',', '.') }}đ × {{ $ct->so_luong }}</small>
            </div>
            <strong>{{ number_format($ct->gia * $ct->so_luong, 0, ',', '.') }}đ</strong>
        </div>
    @endforeach

    <div class="acct-sum">
        <div><span>Tạm tính</span><span>{{ number_format($tamTinh, 0, ',', '.') }}đ</span></div>
        <div><span>Giảm giá (mã và vận chuyển)</span><span>-{{ number_format($donHang->tien_giam, 0, ',', '.') }}đ</span></div>
        <div class="total"><span>Tổng thanh toán</span><span>{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</span></div>
    </div>
</div>

<div class="acct-card">
    <h2 class="acct-sub mt-0">Giao hàng và thanh toán</h2>
    <p class="mb-1"><strong>{{ $donHang->ten_nguoi_nhan }}</strong> · {{ $donHang->so_dien_thoai }}</p>
    <p class="mb-2">{{ $donHang->dia_chi }}</p>
    <p class="mb-0">
        @if ($donHang->phuong_thuc_thanh_toan === 'the')
            Thanh toán bằng thẻ (số cuối {{ $donHang->the_4_so_cuoi }})
        @else
            Thanh toán khi nhận hàng (COD)
        @endif
    </p>
</div>

@if ($donHang->coTheHuy())
    <form method="POST" action="{{ route('tai-khoan.don-hang.huy', $donHang) }}" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
        @csrf
        <button type="submit" class="acct-cancel-btn">Hủy đơn hàng</button>
    </form>
@endif
@endsection
