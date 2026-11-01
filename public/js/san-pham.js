(function () {
    'use strict';

    var root = document.querySelector('.pd-product');
    if (!root) return;

    var ten = root.dataset.ten || 'sản phẩm';
    var stock = parseInt(root.dataset.stock, 10) || 0;
    var images = [];
    try {
        images = JSON.parse(root.dataset.images || '[]');
    } catch (e) {
        images = [];
    }

    var $ = function (id) { return document.getElementById(id); };

    /* ===== Thư viện ảnh (chỉ chạy khi có từ 2 ảnh trở lên) ===== */
    var frame = $('pdFrame');
    var img = $('pdImg');
    var cur = $('pdCur');
    var thumbs = $('pdThumbs');
    var idx = 0;

    function go(i) {
        if (images.length < 2 || !img) return;
        idx = (i + images.length) % images.length;
        img.src = images[idx];
        img.alt = ten + ', ảnh ' + (idx + 1);
        if (cur) cur.textContent = idx + 1;
        if (thumbs) {
            Array.prototype.forEach.call(thumbs.children, function (t, k) {
                if (k === idx) t.setAttribute('aria-current', 'true');
                else t.removeAttribute('aria-current');
            });
        }
    }

    if (images.length > 1) {
        var prev = $('pdPrev');
        var next = $('pdNext');
        if (prev) prev.addEventListener('click', function () { go(idx - 1); });
        if (next) next.addEventListener('click', function () { go(idx + 1); });

        if (thumbs) {
            thumbs.addEventListener('click', function (e) {
                var b = e.target.closest('.pd-thumb');
                if (b) go(parseInt(b.dataset.i, 10));
            });
        }

        // Phím mũi tên trái/phải (không bắt khi đang gõ vào ô nhập)
        document.addEventListener('keydown', function (e) {
            if (e.target.closest && e.target.closest('input, textarea, select')) return;
            if (e.key === 'ArrowLeft') go(idx - 1);
            if (e.key === 'ArrowRight') go(idx + 1);
        });

        // Vuốt trên điện thoại
        var x0 = null;
        frame.addEventListener('touchstart', function (e) {
            x0 = e.touches[0].clientX;
        }, { passive: true });
        frame.addEventListener('touchend', function (e) {
            if (x0 === null) return;
            var d = e.changedTouches[0].clientX - x0;
            if (Math.abs(d) > 40) go(idx + (d < 0 ? 1 : -1));
            x0 = null;
        });
    }

    /* ===== Số lượng ===== */
    var qty = 1;
    var qtyEl = $('pdQty');
    var minus = $('pdMinus');
    var plus = $('pdPlus');

    function renderQty() {
        if (!qtyEl) return;
        qtyEl.textContent = qty;
        minus.disabled = qty <= 1;
        plus.disabled = qty >= stock;
    }

    if (qtyEl) {
        minus.addEventListener('click', function () {
            if (qty > 1) { qty--; renderQty(); }
        });
        plus.addEventListener('click', function () {
            if (qty < stock) { qty++; renderQty(); }
        });
        renderQty();
    }

    /* ===== Thông báo (bản tạm, chờ làm giỏ hàng ở bước sau) ===== */
    var added = $('pdAdded');

    function show(msg) {
        added.textContent = msg;
        added.classList.add('show');
    }

    var addBtn = $('pdAddCart');
    var buyBtn = $('pdBuy');

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            if (stock < 1) return;
            show('Bản xem thử: giỏ hàng sẽ được làm ở bước sau (' + qty + ' × ' + ten + ').');
        });
    }

    if (buyBtn) {
        buyBtn.addEventListener('click', function () {
            if (stock < 1) return;
            show('Bản xem thử: trang đặt hàng sẽ được làm ở bước sau (' + qty + ' × ' + ten + ').');
        });
    }

    /* ===== Sao chép liên kết (nút Z – Zalo) ===== */
    var copyBtn = $('pdCopyLink');

    function copyText(text) {
        // navigator.clipboard chỉ có trên trang https hoặc localhost,
        // còn http://emoloop.test thì không, nên cần cách dự phòng.
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (e) {
                reject(e);
            }
            document.body.removeChild(ta);
        });
    }

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            copyText(window.location.href).then(function () {
                show('Đã sao chép liên kết. Bạn dán vào Zalo để chia sẻ.');
            }).catch(function () {
                show('Không sao chép được, bạn hãy copy liên kết trên thanh địa chỉ.');
            });
        });
    }
})();
