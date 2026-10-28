@php($u = auth()->user())
<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content acc-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="accountModalLabel">Tài khoản</h5>
                    <small class="acc-email">{{ $u->email }}</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form id="accProfileForm" method="POST" action="{{ route('tai-khoan.update') }}" enctype="multipart/form-data" class="auth-form" novalidate>
                    @csrf
                    @method('PATCH')
                    <h6 class="acc-section">Thông tin cá nhân</h6>
                    @if (session('ok_profile'))
                        <div class="acc-ok">{{ session('ok_profile') }}</div>
                    @endif

                    <div class="acc-avatar-row" id="accAvatarRow">
                        <div class="acc-avatar-preview" id="accAvatarPreview">
                            @if ($u->avatar)
                                <img src="{{ asset('storage/'.$u->avatar) }}" alt="">
                            @else
                                <span>{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="auth-field @error('avatar', 'profile') invalid @enderror">
                            <label for="acc-avatar">Ảnh đại diện</label>
                            <input id="acc-avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp">
                            @error('avatar', 'profile')<span class="auth-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <span class="auth-msg" id="accCropErr" hidden></span>

                    <div class="acc-crop" id="accCrop" hidden>
                        <div class="acc-stage" id="accStage">
                            <img id="accCropImg" alt="" draggable="false">
                        </div>
                        <div class="acc-zoom">
                            <i class="bi bi-image"></i>
                            <input type="range" id="accZoom" min="1" max="3" step="0.01" value="1" aria-label="Phóng to, thu nhỏ">
                            <i class="bi bi-image-fill"></i>
                        </div>
                        <p class="acc-hint">Kéo ảnh để chỉnh vị trí, kéo thanh trượt để phóng to hoặc thu nhỏ.</p>
                    </div>

                    <div class="auth-field @error('name', 'profile') invalid @enderror">
                        <label for="acc-name">Họ và tên (tên hiển thị)</label>
                        <input id="acc-name" name="name" type="text" value="{{ old('name', $u->name) }}" autocomplete="name" required>
                        @error('name', 'profile')<span class="auth-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="auth-field @error('so_dien_thoai', 'profile') invalid @enderror">
                        <label for="acc-phone">Số điện thoại</label>
                        <input id="acc-phone" name="so_dien_thoai" type="tel" value="{{ old('so_dien_thoai', $u->so_dien_thoai) }}" autocomplete="tel" required>
                        @error('so_dien_thoai', 'profile')<span class="auth-msg">{{ $message }}</span>@enderror
                    </div>

                    <button class="auth-btn" type="submit">Lưu thông tin</button>
                </form>

                <hr class="acc-divider">

                <form method="POST" action="{{ route('tai-khoan.mat-khau') }}" class="auth-form" novalidate>
                    @csrf
                    @method('PUT')
                    <h6 class="acc-section">Đổi mật khẩu</h6>
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
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('accountModal');
    var form = document.getElementById('accProfileForm');
    var input = document.getElementById('acc-avatar');
    var row = document.getElementById('accAvatarRow');
    var crop = document.getElementById('accCrop');
    var stage = document.getElementById('accStage');
    var img = document.getElementById('accCropImg');
    var zoom = document.getElementById('accZoom');
    var err = document.getElementById('accCropErr');

    var SIZE = 240;
    var OUT = 512;
    var s = 1;
    var min = 1;
    var x = 0;
    var y = 0;
    var ready = false;
    var drag = null;
    var objectUrl = null;

    function clamp() {
        var w = img.naturalWidth * s;
        var h = img.naturalHeight * s;
        x = Math.min(0, Math.max(SIZE - w, x));
        y = Math.min(0, Math.max(SIZE - h, y));
    }

    function apply() {
        clamp();
        img.style.width = img.naturalWidth * s + 'px';
        img.style.height = img.naturalHeight * s + 'px';
        img.style.transform = 'translate(' + x + 'px,' + y + 'px)';
    }

    function setScale(next) {
        var cx = (SIZE / 2 - x) / s;
        var cy = (SIZE / 2 - y) / s;
        s = next;
        x = SIZE / 2 - cx * s;
        y = SIZE / 2 - cy * s;
        apply();
    }

    function resetCrop() {
        ready = false;
        crop.hidden = true;
        row.classList.remove('cropping');
    }

    input.addEventListener('change', function () {
        err.hidden = true;
        resetCrop();
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
        var file = input.files[0];
        if (!file) {
            return;
        }
        objectUrl = URL.createObjectURL(file);
        img.src = objectUrl;
    });

    img.addEventListener('load', function () {
        min = SIZE / Math.min(img.naturalWidth, img.naturalHeight);
        s = min;
        x = (SIZE - img.naturalWidth * s) / 2;
        y = (SIZE - img.naturalHeight * s) / 2;
        zoom.value = 1;
        apply();
        ready = true;
        crop.hidden = false;
        row.classList.add('cropping');
    });

    img.addEventListener('error', function () {
        resetCrop();
        input.value = '';
        err.textContent = 'Không đọc được ảnh này, vui lòng chọn ảnh JPG, PNG hoặc WEBP khác.';
        err.hidden = false;
    });

    zoom.addEventListener('input', function () {
        if (ready) {
            setScale(min * parseFloat(zoom.value));
        }
    });

    stage.addEventListener('wheel', function (e) {
        if (!ready) {
            return;
        }
        e.preventDefault();
        var v = Math.min(3, Math.max(1, parseFloat(zoom.value) + (e.deltaY < 0 ? 0.1 : -0.1)));
        zoom.value = v;
        setScale(min * v);
    }, { passive: false });

    stage.addEventListener('pointerdown', function (e) {
        if (!ready) {
            return;
        }
        drag = { px: e.clientX, py: e.clientY, x: x, y: y };
        stage.setPointerCapture(e.pointerId);
        stage.classList.add('dragging');
    });

    stage.addEventListener('pointermove', function (e) {
        if (!drag) {
            return;
        }
        x = drag.x + e.clientX - drag.px;
        y = drag.y + e.clientY - drag.py;
        apply();
    });

    function endDrag() {
        drag = null;
        stage.classList.remove('dragging');
    }

    stage.addEventListener('pointerup', endDrag);
    stage.addEventListener('pointercancel', endDrag);

    form.addEventListener('submit', function (e) {
        if (!ready) {
            return;
        }
        e.preventDefault();
        var canvas = document.createElement('canvas');
        canvas.width = OUT;
        canvas.height = OUT;
        var side = SIZE / s;
        canvas.getContext('2d').drawImage(img, -x / s, -y / s, side, side, 0, 0, OUT, OUT);
        canvas.toBlob(function (blob) {
            var data = new DataTransfer();
            data.items.add(new File([blob], 'avatar.png', { type: 'image/png' }));
            input.files = data.files;
            form.submit();
        }, 'image/png');
    });

    @if ($errors->profile->any() || $errors->password->any() || session('ok_profile') || session('ok_password'))
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
    @endif
});
</script>