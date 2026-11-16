<?php

namespace App\Http\Controllers;

use App\Listeners\GhiNhoTaiKhoanDangNhap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ChuyenTaiKhoanController extends Controller
{
    private function danhSach(Request $request): array
    {
        return array_map('intval', $request->session()->get(GhiNhoTaiKhoanDangNhap::KEY, []));
    }

    // Chuyển sang tài khoản đã đăng nhập trước đó trong trình duyệt này
    public function chuyen(Request $request, User $user)
    {
        abort_unless(in_array((int) $user->id, $this->danhSach($request), true), 403);

        if ((int) $user->id !== (int) $request->user()->id) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
        }

        return redirect()->route('tai-khoan.ho-so')
            ->with('ok_switch', 'Đã chuyển sang tài khoản ' . $user->name . '.');
    }

    // Thêm tài khoản khác: phải nhập đúng email và mật khẩu, sai thì vẫn giữ tài khoản hiện tại
    public function them(Request $request)
    {
        $data = $request->validateWithBag('them_tk', [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], ['email' => 'email', 'password' => 'mật khẩu']);

        $hienTai = (int) $request->user()->id;
        $khoa = 'them-tk:' . $hienTai . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($khoa, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Bạn thử quá nhiều lần, vui lòng đợi ' . RateLimiter::availableIn($khoa) . ' giây.',
            ])->errorBag('them_tk');
        }

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($khoa, 60);
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->errorBag('them_tk');
        }

        if ((int) $user->id === $hienTai) {
            throw ValidationException::withMessages([
                'email' => 'Tài khoản này đang được đăng nhập.',
            ])->errorBag('them_tk');
        }

        RateLimiter::clear($khoa);

        // Đảm bảo tài khoản hiện tại có trong danh sách (phòng khi đăng nhập từ trước khi có tính năng này)
        $ids = $this->danhSach($request);
        if (! in_array($hienTai, $ids, true)) {
            $ids[] = $hienTai;
            $request->session()->put(GhiNhoTaiKhoanDangNhap::KEY, $ids);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('tai-khoan.ho-so')
            ->with('ok_switch', 'Đã thêm và chuyển sang tài khoản ' . $user->name . '.');
    }

    // Gỡ một tài khoản (không phải tài khoản đang dùng) khỏi danh sách chuyển nhanh
    public function go(Request $request, User $user)
    {
        if ((int) $user->id !== (int) $request->user()->id) {
            $ids = array_values(array_filter(
                $this->danhSach($request),
                fn ($id) => $id !== (int) $user->id
            ));
            $request->session()->put(GhiNhoTaiKhoanDangNhap::KEY, $ids);
        }

        return back();
    }
}
