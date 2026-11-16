@extends('layouts.app')

@section('title', 'Tài khoản – Emo Loop')
@section('body_class', 'acct-page')
@push('styles')
    <link href="{{ asset('css/account.css') }}" rel="stylesheet">
@endpush
@section('content')
@php($u = auth()->user())
<div class="acct-wrap">
    <aside class="acct-side">
        <div class="acct-who">
            <div class="acct-who-avatar">
                @if ($u->avatar)
                    <img src="{{ asset('storage/'.$u->avatar) }}" alt="">
                @else
                    <span>{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="acct-who-text">
                <strong>{{ $u->name }}</strong>
                <small>{{ $u->email }}</small>
            </div>
        </div>

        <nav class="acct-menu" aria-label="Menu tài khoản">
            <a href="{{ route('tai-khoan.ho-so') }}" @class(['active' => request()->routeIs('tai-khoan.ho-so')])>
                <i class="bi bi-person"></i> Hồ sơ và địa chỉ
            </a>
            @if (Route::has('tai-khoan.don-hang'))
                <a href="{{ route('tai-khoan.don-hang') }}" @class(['active' => request()->routeIs('tai-khoan.don-hang*')])>
                    <i class="bi bi-box-seam"></i> Đơn hàng
                </a>
            @endif
            <a href="{{ route('tai-khoan.ma-giam') }}" @class(['active' => request()->routeIs('tai-khoan.ma-giam')])>
                <i class="bi bi-ticket-perforated"></i> Mã giảm giá
            </a>
            <a href="{{ route('tai-khoan.mat-khau.form') }}" @class(['active' => request()->routeIs('tai-khoan.mat-khau.form')])>
                <i class="bi bi-shield-lock"></i> Mật khẩu
            </a>
            <form method="POST" action="{{ url('/logout') }}" class="m-0">
                @csrf
                <button type="submit" class="acct-logout"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
            </form>
        </nav>
            @include('account._chuyen-tai-khoan')
    </aside>

    <section class="acct-main">
        <h1 class="acct-title">@yield('panel_title')</h1>
        @yield('panel')
    </section>
</div>
@endsection
