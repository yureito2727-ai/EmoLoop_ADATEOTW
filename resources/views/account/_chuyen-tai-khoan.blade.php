@php
    $dsId = array_map('intval', session('tk_da_dang_nhap', []));
    if (! in_array((int) auth()->id(), $dsId, true)) {
        $dsId[] = (int) auth()->id();
    }
    $dsTk = \App\Models\User::whereIn('id', $dsId)->get();
@endphp

<div class="acct-switch">
    <div class="acct-switch-title">Tài khoản đã đăng nhập</div>

    @if (session('ok_switch'))
        <div class="acc-ok">{{ session('ok_switch') }}</div>
    @endif

    @foreach ($dsTk as $t)
        @php($dangDung = (int) $t->id === (int) auth()->id())
        <div @class(['acct-switch-row', 'current' => $dangDung])>
            <span class="acct-switch-avatar">
                @if ($t->avatar)
                    <img src="{{ asset('storage/'.$t->avatar) }}" alt="">
                @else
                    {{ mb_strtoupper(mb_substr($t->name, 0, 1)) }}
                @endif
            </span>
            <span class="acct-switch-text">
                <strong>{{ $t->name }}</strong>
                <small>{{ $t->email }}</small>
            </span>
            @if ($dangDung)
                <span class="acct-switch-now">Đang dùng</span>
            @else
                <form method="POST" action="{{ route('tai-khoan.chuyen', $t) }}" class="m-0">
                    @csrf
                    <button type="submit" class="acct-switch-btn">Chuyển</button>
                </form>
                <form method="POST" action="{{ route('tai-khoan.go-tk', $t) }}" class="m-0">
                    @csrf
                    <button type="submit" class="acct-switch-x" title="Gỡ khỏi danh sách" aria-label="Gỡ khỏi danh sách"><i class="bi bi-x-lg"></i></button>
                </form>
            @endif
        </div>
    @endforeach

    <button type="button" class="acct-switch-add" data-bs-toggle="modal" data-bs-target="#addAccountModal">
        <i class="bi bi-plus-lg"></i> Thêm tài khoản
    </button>
</div>

<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content acct-modal">
            <form method="POST" action="{{ route('tai-khoan.them-tk') }}" class="auth-form" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountLabel">Thêm tài khoản</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="acct-note">Đăng nhập tài khoản khác. Bạn vẫn giữ tài khoản hiện tại để chuyển qua lại.</p>

                    <div class="auth-field @error('email', 'them_tk') invalid @enderror">
                        <label for="add-email">Email</label>
                        <input id="add-email" name="email" type="email" value="{{ old('email') }}" autocomplete="off" required>
                        @error('email', 'them_tk')<span class="auth-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-field @error('password', 'them_tk') invalid @enderror">
                        <label for="add-pass">Mật khẩu</label>
                        <input id="add-pass" name="password" type="password" autocomplete="off" required>
                        @error('password', 'them_tk')<span class="auth-msg">{{ $message }}</span>@enderror
                    </div>

                    <button class="auth-btn" type="submit">Đăng nhập và chuyển</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.acct-switch { margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.08); }
.acct-switch-title { font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: rgba(243,238,246,.55); margin-bottom: 8px; }
.acct-switch-row { display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 12px; }
.acct-switch-row.current { background: rgba(255,255,255,.05); }
.acct-switch-avatar { flex: none; width: 34px; height: 34px; border-radius: 50%; overflow: hidden; display: grid; place-items: center; font-weight: 800; color: #1a1a1a; background: linear-gradient(135deg, #ff7eb3, #ffd36b); }
.acct-switch-avatar img { width: 100%; height: 100%; object-fit: cover; }
.acct-switch-text { flex: 1; min-width: 0; display: flex; flex-direction: column; color: #f3eef6; line-height: 1.25; }
.acct-switch-text small { color: rgba(243,238,246,.55); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.acct-switch-now { font-size: .75rem; font-weight: 700; color: #ffd36b; }
.acct-switch-btn { padding: 4px 12px; border-radius: 999px; border: 1px solid rgba(255,211,107,.6); background: none; color: #ffd36b; font-weight: 700; font-size: .82rem; }
.acct-switch-btn:hover { background: rgba(255,211,107,.12); }
.acct-switch-x { border: 0; background: none; color: rgba(243,238,246,.45); padding: 4px; }
.acct-switch-x:hover { color: #ff8a8a; }
.acct-switch-add { width: 100%; margin-top: 6px; padding: 8px; border-radius: 12px; border: 1px dashed rgba(255,255,255,.25); background: none; color: rgba(243,238,246,.8); font-weight: 600; }
.acct-switch-add:hover { border-color: #ffd36b; color: #fff; }
.acct-modal { background: #17121d; color: #f3eef6; border: 1px solid rgba(255,255,255,.1); border-radius: 18px; }
.acct-modal .modal-header { border-bottom: 1px solid rgba(255,255,255,.08); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('addAccountModal');
    document.body.appendChild(el); // đưa cửa sổ ra ngoài thanh menu để không bị lớp nền tối đè lên
    @if ($errors->getBag('them_tk')->any())
    bootstrap.Modal.getOrCreateInstance(el).show();
    @endif
});
</script>
@endpush
