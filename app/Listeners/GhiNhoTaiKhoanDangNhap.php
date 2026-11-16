<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

// Mỗi lần đăng nhập thành công, nhớ id tài khoản vào session để có thể chuyển qua lại.
// Laravel tự nhận listener trong app/Listeners nhờ type-hint Login ở hàm handle.
class GhiNhoTaiKhoanDangNhap
{
    public const KEY = 'tk_da_dang_nhap';

    public function handle(Login $event): void
    {
        if ($event->guard !== 'web') {
            return;
        }

        $ids = array_map('intval', session()->get(self::KEY, []));
        $id = (int) $event->user->getAuthIdentifier();

        if (! in_array($id, $ids, true)) {
            $ids[] = $id;
            session()->put(self::KEY, $ids);
        }
    }
}
