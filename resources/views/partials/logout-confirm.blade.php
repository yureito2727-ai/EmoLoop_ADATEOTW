@php($soTk = count(session('tk_da_dang_nhap', [])))
<div class="modal fade" id="logoutConfirm" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content lc-content">
            <div class="modal-body text-center">
                <i class="bi bi-box-arrow-right lc-icon"></i>
                <h6 id="logoutConfirmLabel" class="lc-title">Đăng xuất?</h6>
                <p class="lc-text">
                    @if ($soTk > 1)
                        Bạn sẽ đăng xuất khỏi cả {{ $soTk }} tài khoản đã đăng nhập trên trình duyệt này.
                    @else
                        Bạn có chắc muốn đăng xuất không?
                    @endif
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="lc-btn lc-stay" data-bs-dismiss="modal">Ở lại</button>
                    <button type="button" class="lc-btn lc-yes" id="logoutConfirmYes">Đăng xuất</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.lc-content { background: #17121d; color: #f3eef6; border: 1px solid rgba(255,255,255,.1); border-radius: 18px; }
.lc-icon { font-size: 2rem; color: #ffd36b; }
.lc-title { font-weight: 800; margin: 6px 0; }
.lc-text { color: rgba(243,238,246,.75); font-size: .95rem; }
.lc-btn { padding: 8px 20px; border-radius: 999px; font-weight: 700; border: 1px solid transparent; }
.lc-stay { background: none; border-color: rgba(255,255,255,.3); color: #f3eef6; }
.lc-yes { background: linear-gradient(90deg, #ff7eb3, #ffd36b); color: #1a1a1a; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('logoutConfirm'));
    var pending = null;

    // Chặn mọi form gửi tới /logout (nút trên header và trong trang tài khoản) để hỏi xác nhận
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.action) { return; }
        var path;
        try { path = new URL(form.action, location.href).pathname; } catch (err) { return; }
        if (!/\/logout$/.test(path)) { return; }
        e.preventDefault();
        pending = form;
        modal.show();
    });

    document.getElementById('logoutConfirmYes').addEventListener('click', function () {
        if (pending) { pending.submit(); }
    });
});
</script>
