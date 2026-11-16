@extends('account.layout')

@section('title', 'Mã giảm giá – Emo Loop')
@section('panel_title', 'Mã giảm giá')

@section('panel')
<p class="acct-note">Mã còn dùng được sẽ tự áp dụng khi bạn thanh toán sản phẩm tương ứng. Nhận thêm mã bằng cách chơi game.</p>

<h2 class="acct-sub">Còn dùng được ({{ $conDung->count() }})</h2>
@forelse ($conDung as $m)
    @include('account._ma-giam-the', ['m' => $m, 'tt' => 'con-dung'])
@empty
    <div class="acct-empty">
        Bạn chưa có mã nào. <a href="{{ route('game.nhan-vat') }}">Chơi game</a> để nhận mã giảm giá.
    </div>
@endforelse

@if ($daDung->isNotEmpty())
    <h2 class="acct-sub">Đã dùng ({{ $daDung->count() }})</h2>
    @foreach ($daDung as $m)
        @include('account._ma-giam-the', ['m' => $m, 'tt' => 'da-dung'])
    @endforeach
@endif

@if ($hetHan->isNotEmpty())
    <h2 class="acct-sub">Hết hạn hoặc đã tắt ({{ $hetHan->count() }})</h2>
    @foreach ($hetHan as $m)
        @include('account._ma-giam-the', ['m' => $m, 'tt' => 'het-han'])
    @endforeach
@endif
@endsection
