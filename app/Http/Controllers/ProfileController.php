<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->merge([
            'so_dien_thoai' => preg_replace('/\s+/', '', (string) $request->input('so_dien_thoai')),
        ]);

        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'so_dien_thoai' => ['required', 'regex:/^\d{9,11}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $user->name = $data['name'];
        $user->so_dien_thoai = $data['so_dien_thoai'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return back()->with('ok_profile', 'Đã cập nhật thông tin.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($data['password']);
        $user->save();

        return back()->with('ok_password', 'Đã đổi mật khẩu.');
    }
}