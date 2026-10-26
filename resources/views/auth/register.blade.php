@extends('layouts.auth')

@section('title', 'Đăng ký – Emo Loop')

@section('form')
<form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
    @csrf
    <div class="auth-field @error('name') invalid @enderror">
        <label for="name">Họ và tên</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nguyễn Văn A" autocomplete="name" required autofocus>
        @error('name')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('email') invalid @enderror">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="ban@email.com" autocomplete="email" required>
        @error('email')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('so_dien_thoai') invalid @enderror">
        <label for="so_dien_thoai">Số điện thoại</label>
        <input id="so_dien_thoai" name="so_dien_thoai" type="tel" value="{{ old('so_dien_thoai') }}" placeholder="0901 234 567" autocomplete="tel" required>
        @error('so_dien_thoai')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('password') invalid @enderror">
        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required>
        @error('password')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field">
        <label for="password_confirmation">Nhập lại mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
    </div>

    <button class="auth-btn" type="submit">Tạo tài khoản</button>
    <p class="auth-note">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
</form>
@endsection