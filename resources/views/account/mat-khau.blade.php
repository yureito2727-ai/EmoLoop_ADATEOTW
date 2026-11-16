@extends('account.layout')

@section('title', 'Mật khẩu – Emo Loop')
@section('panel_title', 'Đổi mật khẩu')

@section('panel')
<div class="acct-card acc-modal">
    <form method="POST" action="{{ route('tai-khoan.mat-khau') }}" class="auth-form" novalidate>
        @csrf
        @method('PUT')
        @if (session('ok_password'))
            <div class="acc-ok">{{ session('ok_password') }}</div>
        @endif

        <div class="auth-field @error('password', 'password') invalid @enderror">
            <label for="acc-pass">Mật khẩu mới</label>
            <input id="acc-pass" name="password" type="password" autocomplete="new-password" required>
            @error('password', 'password')<span class="auth-msg">{{ $message }}</span>@enderror
        </div>

        <div class="auth-field">
            <label for="acc-pass2">Nhập lại mật khẩu mới</label>
            <input id="acc-pass2" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>

        <button class="auth-btn" type="submit">Đổi mật khẩu</button>
    </form>
</div>
@endsection
