<?php

namespace App\Http\Controllers;

use App\Models\DiaChi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    public function updateAddress(Request $request): RedirectResponse
    {
        $request->merge([
            'sdt_nguoi_nhan' => preg_replace('/\s+/', '', (string) $request->input('sdt_nguoi_nhan')),
        ]);

        $data = $request->validateWithBag('address', [
            'ten_nguoi_nhan' => ['nullable', 'string', 'max:255'],
            'sdt_nguoi_nhan' => ['nullable', 'regex:/^\d{9,11}$/'],
            'quoc_gia' => ['required', Rule::in(DiaChi::QUOC_GIA)],
            'tinh_thanh' => ['required', 'string', 'max:150'],
            'phuong_xa' => ['required_if:quoc_gia,Việt Nam', 'nullable', 'string', 'max:150'],
            'dia_chi_cu_the' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $ten = trim((string) ($data['ten_nguoi_nhan'] ?? ''));
        $sdt = (string) ($data['sdt_nguoi_nhan'] ?? '');

        DiaChi::updateOrCreate(
            ['user_id' => $user->id],
            [
                'ten_nguoi_nhan' => ($ten === '' || $ten === $user->name) ? null : $ten,
                'so_dien_thoai' => ($sdt === '' || $sdt === $user->so_dien_thoai) ? null : $sdt,
                'quoc_gia' => $data['quoc_gia'],
                'tinh_thanh' => $data['tinh_thanh'],
                'phuong_xa' => $data['phuong_xa'] ?? null,
                'dia_chi_cu_the' => $data['dia_chi_cu_the'],
            ]
        );

        return back()->with('ok_address', 'Đã lưu địa chỉ nhận hàng.');
    }
}