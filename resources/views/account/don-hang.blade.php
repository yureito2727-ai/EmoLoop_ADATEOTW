@extends('account.layout')

@section('title', 'Đơn hàng – Emo Loop')
@section('panel_title', 'Đơn hàng của tôi')

@section('panel')
<div class="acct-tabs">
    <a href="{{ route('tai-khoan.don-hang') }}" @class(['active' => ! $loc])>Tất cả</a>
    @foreach (\App\Models\DonHang::TRANG_THAI as $k => $ten)
        <a href="{{ route('tai-khoan.don-hang', ['trang_thai' => $k]) }}" @class(['active' => $loc === $k])>{{ $ten }}</a>
    @endforeach
</div>

@forelse ($donHangs as $d)
    <a href="{{ route('tai-khoan.don-hang.show', $d) }}" class="acct-order">
        <div class="acct-order-top">
            <strong>Đơn #{{ $d->id }}</strong>
            <span class="acct-badge st-{{ $d->trang_thai }}">{{ $d->nhan_trang_thai }}</span>
        </div>
        <div class="acct-order-mid">
            {{ $d->chiTiets->first()?->sanPham?->ten }}@if ($d->chiTiets->count() > 1) và các sản phẩm khác @endif
        </div>
        <div class="acct-order-bot">
            <small>{{ $d->created_at?->format('d/m/Y H:i') }} · {{ $d->so_san_pham }} sản phẩm</small>
            <strong>{{ number_format($d->tong_tien, 0, ',', '.') }}đ</strong>
        </div>
    </a>
@empty
    <div class="acct-empty">
        Chưa có đơn hàng nào{{ $loc ? ' ở trạng thái này' : '' }}. <a href="{{ route('shop') }}">Đi tới cửa hàng</a>    
    </div>
@endforelse

<div class="mt-3">{{ $donHangs->links() }}</div>
@endsection
