@extends('layouts.app')
@section('title', 'Giỏ hàng – Emo Loop')
@section('body_class', 'page-cart')

@section('content')
<style>
    body.page-cart{background:#000}
.gh{--g:linear-gradient(90deg,#ff5fc2 0%,#ff8aa0 35%,#ffb07f 65%,#ffdd57 100%);--surface:#0e0e10;--line:#2a2a2e;--muted:#a09ca2;--yellow:#ffdd57;--pink:#ff5fc2;--err:#ff7a8a;color:#f5f3f4}
.gh h1{font-weight:800;background:var(--g);-webkit-background-clip:text;background-clip:text;color:transparent;display:inline-block}
.gh .sub{color:var(--muted);margin-bottom:28px}
.gh .card-x{background:var(--surface);border:1px solid var(--line);border-radius:24px;position:relative}
.gh .card-x::before{content:"";position:absolute;inset:0 0 auto 0;height:4px;border-radius:24px 24px 0 0;background:var(--g)}
.gh .cols,.gh .item{display:grid;grid-template-columns:32px minmax(0,1fr) 110px 140px 120px 70px;gap:16px;align-items:center}
.gh .cols{padding:0 28px 12px;color:var(--muted);font-size:.85rem}
.gh .cols .c{text-align:center}
.gh .list{display:grid;gap:14px}
.gh .item{padding:20px 28px;overflow:hidden}
.gh .item.off{opacity:.55}
.gh .prod{display:flex;gap:14px;align-items:center;min-width:0}
.gh .thumb{width:72px;height:72px;flex:none;border-radius:18px;background:#000;border:1px solid var(--line);overflow:hidden;display:grid;place-items:center;color:var(--muted);font-size:.75rem}
.gh .thumb img{width:100%;height:100%;object-fit:cover}
.gh .name{font-weight:700;color:inherit;text-decoration:none;overflow-wrap:anywhere}
.gh .code{font-size:.8rem;color:var(--pink)}
.gh .code.none{color:var(--muted)}
.gh .price,.gh .total{text-align:center}
.gh .total .old{display:block;color:var(--muted);text-decoration:line-through;font-size:.8rem}
.gh .total .now{color:var(--yellow);font-weight:700}
.gh .qty{justify-self:center;display:grid;grid-template-columns:36px 40px 36px;align-items:center;background:#000;border:1px solid var(--line);border-radius:16px;padding:3px}
.gh .qty button{background:none;border:0;border-radius:12px;height:34px;color:var(--muted);font-size:1.1rem}
.gh .qty button:hover{background:var(--g);color:#1a1a1a}
.gh .qty span{text-align:center;font-weight:700}
.gh .del{background:none;border:0;color:var(--err);text-decoration:underline;justify-self:center;font-size:.9rem}
.gh .del:hover{color:var(--yellow)}
.gh input[type=checkbox]{accent-color:var(--pink);width:18px;height:18px;margin:0}
.gh .bar{margin-top:28px;padding:20px 28px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;position:sticky;bottom:12px;background:var(--surface);z-index:5}
.gh .bar label{display:flex;align-items:center;gap:10px;font-size:.9rem}
.gh .clear{background:none;border:0;color:var(--yellow);text-decoration:underline;font-size:.9rem}
.gh .sum{margin-left:auto}
.gh .sum b{color:var(--yellow);font-size:1.15rem;margin-left:6px}
.gh .btn-buy{font-weight:700;color:#1a1a1a;background:var(--g);border:0;border-radius:16px;padding:14px 44px}
.gh .btn-buy:hover{filter:brightness(1.08)}
.gh .btn-buy:disabled{filter:grayscale(1) brightness(.6)}
.gh .empty{text-align:center;padding:56px 24px;color:var(--muted)}
.gh .toast-x{position:fixed;left:50%;bottom:96px;transform:translateX(-50%);background:var(--surface);border:1px solid var(--yellow);color:var(--yellow);border-radius:14px;padding:10px 18px;font-size:.9rem;display:none;z-index:10}
.gh .toast-x.show{display:block}
@media(max-width:820px){
.gh .cols{display:none}
.gh .item{grid-template-columns:28px minmax(0,1fr);gap:12px 14px;padding:18px}
.gh .item .prod{grid-column:2}
.gh .item .price,.gh .item .qty,.gh .item .total,.gh .item .del{grid-column:2;justify-self:start;text-align:left}
.gh .bar{padding:16px 18px}
.gh .sum{margin-left:0;width:100%}
.gh .btn-buy{width:100%}
}
</style>

<div class="gh container py-4" style="max-width:1080px">
    <h1>Giỏ hàng của bạn</h1>
    <p class="sub">Chọn sản phẩm muốn mua. Mã giảm giá nhận từ game được áp tự động.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div id="empty" class="card-x empty" @if($items->isNotEmpty()) style="display:none" @endif>
        <p>Giỏ hàng đang trống. Chọn vài món lưu niệm để bắt đầu.</p>
        <a class="btn-buy d-inline-block text-decoration-none" href="{{ url('/shop') }}">Xem shop</a>
    </div>

    <div id="content" @if($items->isEmpty()) style="display:none" @endif>
        <div class="cols" aria-hidden="true">
            <span></span><span>Sản phẩm</span><span class="c">Đơn giá</span><span class="c">Số lượng</span><span class="c">Thành tiền</span><span class="c">Thao tác</span>
        </div>

        <div class="list" id="list">
            @foreach ($items as $it)
                <article class="card-x item" data-id="{{ $it['id'] }}" data-gia="{{ $it['gia'] }}" data-giam="{{ $it['giam'] }}" data-qty="{{ $it['so_luong'] }}">
                    <input type="checkbox" class="pick" checked aria-label="Chọn {{ $it['ten'] }}">
                    <div class="prod">
                        <div class="thumb">
                            @if ($it['anh'])
                                <img src="{{ asset('storage/' . $it['anh']) }}" alt="{{ $it['ten'] }}">
                            @else
                                Ảnh
                            @endif
                        </div>
                        <div>
                            <a class="name" href="{{ $it['url'] }}">{{ $it['ten'] }}</a>
                            @if ($it['ma'])
                                <div class="code">Mã giảm: {{ $it['ma'] }} ({{ $it['nhan'] }})</div>
                            @else
                                <div class="code none">Chưa áp mã giảm</div>
                            @endif
                        </div>
                    </div>
                    <div class="price">{{ number_format($it['gia'], 0, ',', '.') }}đ</div>
                    <div class="qty">
                        <button type="button" class="minus" aria-label="Giảm số lượng">−</button>
                        <span class="q">{{ $it['so_luong'] }}</span>
                        <button type="button" class="plus" aria-label="Tăng số lượng">+</button>
                    </div>
                    <div class="total"><span class="old"></span><span class="now"></span></div>
                    <button type="button" class="del">Xóa</button>
                </article>
            @endforeach
        </div>

        <section class="card-x bar" aria-label="Tổng thanh toán">
            <label><input type="checkbox" id="all" checked> Chọn tất cả sản phẩm</label>
            <button type="button" class="clear" id="clearAll">Xóa hết</button>
            <div class="sum">Tổng cộng (<span id="count">0</span> sản phẩm): <b id="sum">0đ</b></div>
            <button type="button" class="btn-buy" id="buy">Mua</button>
        </section>
    </div>

    <div class="toast-x" id="toast" role="status"></div>
</div>

<script>
(function () {
    var csrf = '{{ csrf_token() }}';
    var base = '{{ url('/gio-hang') }}';
    var list = document.getElementById('list');
    var all = document.getElementById('all');
    var buy = document.getElementById('buy');
    var toast = document.getElementById('toast');
    var timer;

    function vnd(n) {
        return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + 'đ';
    }

    function say(t) {
        toast.textContent = t;
        toast.classList.add('show');
        clearTimeout(timer);
        timer = setTimeout(function () { toast.classList.remove('show'); }, 2200);
    }

    function send(method, url, body) {
        return fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: body ? JSON.stringify(body) : null
        }).then(function (r) {
            if (!r.ok) { throw new Error('fail'); }
            return r.json();
        });
    }

    function rows() {
        return Array.prototype.slice.call(list.querySelectorAll('.item'));
    }

    function refresh() {
        var all_rows = rows();
        var picked = 0, sum = 0;

        if (!all_rows.length) {
            document.getElementById('content').style.display = 'none';
            document.getElementById('empty').style.display = '';
            return;
        }

        all_rows.forEach(function (row) {
            var gia = +row.dataset.gia, giam = +row.dataset.giam, qty = +row.dataset.qty;
            var goc = gia * qty, net = (gia - giam) * qty;
            var on = row.querySelector('.pick').checked;
            row.classList.toggle('off', !on);
            row.querySelector('.q').textContent = qty;
            row.querySelector('.now').textContent = vnd(net);
            row.querySelector('.old').textContent = giam > 0 ? vnd(goc) : '';
            row.querySelector('.old').style.textDecoration = giam > 0 ? 'line-through' : 'none';
            if (on) { picked++; sum += net; }
        });

        all.checked = picked === all_rows.length;
        document.getElementById('count').textContent = picked;
        document.getElementById('sum').textContent = vnd(sum);
        buy.disabled = !picked;
    }

    list.addEventListener('click', function (e) {
        var row = e.target.closest('.item');
        if (!row) { return; }
        var id = row.dataset.id;

        if (e.target.classList.contains('plus') || e.target.classList.contains('minus')) {
            var cur = +row.dataset.qty;
            var next = e.target.classList.contains('plus') ? Math.min(99, cur + 1) : Math.max(1, cur - 1);
            if (next === cur) { return; }
            row.dataset.qty = next;
            refresh();
            send('PATCH', base + '/' + id, { so_luong: next }).catch(function () {
                row.dataset.qty = cur;
                refresh();
                say('Không cập nhật được số lượng.');
            });
        }

        if (e.target.classList.contains('del')) {
            send('DELETE', base + '/' + id).then(function () {
                row.remove();
                refresh();
            }).catch(function () { say('Không xóa được sản phẩm.'); });
        }
    });

    list.addEventListener('change', function (e) {
        if (e.target.classList.contains('pick')) { refresh(); }
    });

    all.addEventListener('change', function () {
        rows().forEach(function (row) { row.querySelector('.pick').checked = all.checked; });
        refresh();
    });

    document.getElementById('clearAll').addEventListener('click', function () {
        send('DELETE', base).then(function () {
            list.innerHTML = '';
            refresh();
        }).catch(function () { say('Không xóa được giỏ hàng.'); });
    });

    buy.addEventListener('click', function () {
        say('Bước đặt hàng sẽ làm ở phần tiếp theo.');
    });

    refresh();
})();
</script>
@endsection
