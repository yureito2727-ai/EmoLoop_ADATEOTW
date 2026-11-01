@php($dc = $u->diaChi)
<form id="accAddressForm" method="POST" action="{{ route('tai-khoan.dia-chi') }}" class="auth-form" novalidate data-province="{{ old('tinh_thanh', $dc?->tinh_thanh) }}" data-ward="{{ old('phuong_xa', $dc?->phuong_xa) }}">
    @csrf
    @method('PUT')
    <h6 class="acc-section">Địa chỉ nhận hàng</h6>
    @if (session('ok_address'))
        <div class="acc-ok">{{ session('ok_address') }}</div>
    @endif

    <div class="auth-field @error('ten_nguoi_nhan', 'address') invalid @enderror">
        <label for="acc-recv-name">Tên người nhận</label>
        <input id="acc-recv-name" name="ten_nguoi_nhan" type="text" value="{{ old('ten_nguoi_nhan', $dc?->ten_nguoi_nhan ?? $u->name) }}" autocomplete="off">
        <span class="acc-help">Mặc định là tên hiển thị của tài khoản, đổi nếu muốn gửi cho người khác.</span>
        @error('ten_nguoi_nhan', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('sdt_nguoi_nhan', 'address') invalid @enderror">
        <label for="acc-recv-phone">Số điện thoại người nhận</label>
        <input id="acc-recv-phone" name="sdt_nguoi_nhan" type="tel" value="{{ old('sdt_nguoi_nhan', $dc?->so_dien_thoai ?? $u->so_dien_thoai) }}" autocomplete="off">
        <span class="acc-help">Chỉ dùng cho đơn hàng, không thay đổi số điện thoại tài khoản.</span>
        @error('sdt_nguoi_nhan', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="auth-field @error('quoc_gia', 'address') invalid @enderror">
        <label for="acc-country">Quốc gia</label>
        <select id="acc-country" name="quoc_gia">
            @foreach (\App\Models\DiaChi::QUOC_GIA as $qg)
                <option value="{{ $qg }}" @selected(old('quoc_gia', $dc?->quoc_gia ?? 'Việt Nam') === $qg)>{{ $qg }}</option>
            @endforeach
        </select>
        @error('quoc_gia', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <div class="acc-grid">
        <div class="auth-field @error('tinh_thanh', 'address') invalid @enderror">
            <label for="acc-prov">Tỉnh / Thành phố</label>
            <select id="acc-prov" name="tinh_thanh">
                <option value="">Đang tải…</option>
            </select>
            <input id="acc-prov-txt" name="tinh_thanh" type="text" value="{{ old('tinh_thanh', $dc?->tinh_thanh) }}" autocomplete="off" hidden disabled>
            @error('tinh_thanh', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
        </div>

        <div class="auth-field @error('phuong_xa', 'address') invalid @enderror">
            <label for="acc-ward">Phường / Xã</label>
            <select id="acc-ward" name="phuong_xa">
                <option value="">Chọn tỉnh / thành phố trước</option>
            </select>
            <input id="acc-ward-txt" name="phuong_xa" type="text" value="{{ old('phuong_xa', $dc?->phuong_xa) }}" autocomplete="off" hidden disabled>
            @error('phuong_xa', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
        </div>
    </div>

    <span class="acc-help" id="accAddrNote" hidden>Không tải được danh sách địa danh, bạn hãy nhập tay.</span>

    <div class="auth-field @error('dia_chi_cu_the', 'address') invalid @enderror">
        <label for="acc-street">Địa chỉ cụ thể</label>
        <input id="acc-street" name="dia_chi_cu_the" type="text" value="{{ old('dia_chi_cu_the', $dc?->dia_chi_cu_the) }}" placeholder="Số nhà, tên đường, khu vực" autocomplete="off">
        @error('dia_chi_cu_the', 'address')<span class="auth-msg">{{ $message }}</span>@enderror
    </div>

    <button class="auth-btn" type="submit">Lưu địa chỉ</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('accountModal');
    var form = document.getElementById('accAddressForm');
    var country = document.getElementById('acc-country');
    var note = document.getElementById('accAddrNote');
    var prov = { sel: document.getElementById('acc-prov'), txt: document.getElementById('acc-prov-txt') };
    var ward = { sel: document.getElementById('acc-ward'), txt: document.getElementById('acc-ward-txt') };
    var API = 'https://provinces.open-api.vn/api/v2/';
    var saved = { province: form.dataset.province || '', ward: form.dataset.ward || '' };
    var started = false;
    var provFail = false;
    var wardFail = false;
    var wardReq = 0;

    function isVietnam() {
        return country.value === 'Việt Nam';
    }

    function toggle(field, asText) {
        field.sel.hidden = asText;
        field.sel.disabled = asText;
        field.txt.hidden = !asText;
        field.txt.disabled = !asText;
    }

    function applyMode() {
        var vn = isVietnam();
        var provText = !vn || provFail;
        var wardText = provText || wardFail;
        toggle(prov, provText);
        toggle(ward, wardText);
        note.hidden = !(vn && (provFail || wardFail));
    }

    function fetchJson(url) {
        return fetch(url, { headers: { Accept: 'application/json' } }).then(function (res) {
            if (!res.ok) {
                throw new Error('http');
            }
            return res.json();
        });
    }

    function fillSelect(select, items, selected, placeholder) {
        select.innerHTML = '';
        var first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder;
        select.appendChild(first);
        var found = false;
        items.forEach(function (item) {
            var option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            if (item.code !== undefined && item.code !== null) {
                option.dataset.code = item.code;
            }
            if (selected && item.name === selected) {
                option.selected = true;
                found = true;
            }
            select.appendChild(option);
        });
        if (selected && !found) {
            var extra = document.createElement('option');
            extra.value = selected;
            extra.textContent = selected;
            extra.selected = true;
            select.appendChild(extra);
        }
    }

    function loadWards(selectedWard) {
        selectedWard = selectedWard || '';
        var mine = ++wardReq;
        var option = prov.sel.selectedOptions[0];
        var code = option ? option.dataset.code : '';
        wardFail = false;
        ward.txt.value = '';
        if (!code) {
            fillSelect(ward.sel, [], selectedWard, 'Chọn phường / xã');
            applyMode();
            return;
        }
        fillSelect(ward.sel, [], '', 'Đang tải…');
        applyMode();
        fetchJson(API + 'p/' + code + '?depth=2').then(function (data) {
            if (mine !== wardReq) {
                return;
            }
            var list = (data && data.wards) || [];
            if (!list.length) {
                throw new Error('empty');
            }
            fillSelect(ward.sel, list, selectedWard, 'Chọn phường / xã');
            applyMode();
        }).catch(function () {
            if (mine !== wardReq) {
                return;
            }
            wardFail = true;
            ward.txt.value = selectedWard;
            applyMode();
        });
    }

    function start() {
        if (started || !isVietnam()) {
            return;
        }
        started = true;
        fetchJson(API + 'p/').then(function (list) {
            if (!Array.isArray(list) || !list.length) {
                throw new Error('empty');
            }
            fillSelect(prov.sel, list, saved.province, 'Chọn tỉnh / thành phố');
            applyMode();
            loadWards(saved.ward);
        }).catch(function () {
            provFail = true;
            applyMode();
        });
    }

    prov.sel.addEventListener('change', function () {
        loadWards('');
    });

    country.addEventListener('change', function () {
        prov.txt.value = '';
        ward.txt.value = '';
        wardFail = false;
        if (started && !provFail) {
            prov.sel.value = '';
            fillSelect(ward.sel, [], '', 'Chọn tỉnh / thành phố trước');
        }
        applyMode();
        start();
    });

    modalEl.addEventListener('show.bs.modal', start);
    applyMode();
});
</script>