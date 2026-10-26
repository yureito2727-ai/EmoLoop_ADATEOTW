@extends('layouts.app')

@section('body_class', 'page-auth')

@section('content')
<div class="auth-main">
    <section class="auth-intro">
        <h1>Cùng tận hưởng câu chuyện nào!</h1>
        <p>Đăng nhập để chơi Emo Loop, thêm đồ lưu niệm vào giỏ và dùng mã giảm giá bạn nhận được từ game.</p>
        <ul>
            <li>Chinh phục nhân vật để nhận mã giảm giá</li>
            <li>Sản phẩm thiết kế độc quyền</li>
            <li>Đặt hàng nhanh bằng COD hoặc thẻ</li>
        </ul>
    </section>

    <section class="auth-card" aria-label="Tài khoản">
        <div class="auth-tabs">
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Đăng nhập</a>
            <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Đăng ký</a>
        </div>
        @yield('form')
    </section>
</div>
@endsection