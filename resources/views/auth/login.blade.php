@extends('layouts.auth')

@section('title', 'Đăng nhập – Emo Loop')

@section('form')
<form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
    @csrf
    <div class="auth-field @error('email') invalid @enderror">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="ban@email.com" autocomplete="email" required autofocus>
        @error('email')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('password') invalid @enderror">
        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        @error('password')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-row">
        <label class="auth-check">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ghi nhớ đăng nhập
        </label>
    </div>

    <button class="auth-btn" type="submit">Đăng nhập</button>
    <p class="auth-note">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
</form>
@endsection