@extends('layouts.app')

@section('title', $sp->ten . ' – Emo Loop')
@section('body_class', 'page-product')

@push('styles')
    <link href="{{ asset('css/san-pham.css') }}" rel="stylesheet">
@endpush

@php
    // Đổi đường dẫn trong DB thành địa chỉ ảnh dùng được trên web
    $urls = array_map(fn ($p) => asset('storage/' . $p), $anh);
    $tong = count($urls);
    $conHang = $sp->ton_kho > 0;
    $linkTrang = urlencode(url()->current());
@endphp

@section('content')
<div class="pd-wrap">

    {{-- Nút quay về shop (đúng loại sản phẩm đang xem) --}}
    <a class="pd-back" href="{{ route('shop', ['loai' => $sp->loai_san_pham_id]) }}"
       aria-label="Quay lại cửa hàng" title="Quay lại cửa hàng">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="pd-product"
         data-ten="{{ $sp->ten }}"
         data-stock="{{ $sp->ton_kho }}"
         data-images="{{ json_encode($urls) }}">

        {{-- Thư viện ảnh --}}
        <section class="pd-gallery" aria-label="Ảnh sản phẩm">
            <div class="pd-stage">
                @if ($tong > 1)
                    <button class="pd-arrow prev" id="pdPrev" type="button" aria-label="Ảnh trước"></button>
                @else
                    <span></span>
                @endif

                <div class="pd-frame" id="pdFrame" aria-live="polite">
                    @if ($tong > 0)
                        <img id="pdImg" src="{{ $urls[0] }}" alt="{{ $sp->ten }}, ảnh 1">
                    @else
                        <span class="pd-ph">Ảnh cụ thể</span>
                    @endif

                    @if ($tong > 1)
                        <span class="pd-counter"><span id="pdCur">1</span> / {{ $tong }}</span>
                    @endif
                </div>

                @if ($tong > 1)
                    <button class="pd-arrow next" id="pdNext" type="button" aria-label="Ảnh sau"></button>
                @else
                    <span></span>
                @endif
            </div>

            @if ($tong > 1)
                <div class="pd-thumbs" id="pdThumbs">
                    @foreach ($urls as $i => $url)
                        <button class="pd-thumb" type="button" data-i="{{ $i }}"
                                aria-label="Xem ảnh {{ $i + 1 }}"
                                @if ($i === 0) aria-current="true" @endif>
                            <img src="{{ $url }}" alt="" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Thông tin sản phẩm --}}
        <section class="pd-info">
            <h1>{{ $sp->ten }}</h1>
            <p class="pd-price">{{ number_format($sp->gia, 0, ',', '.') }}đ</p>

            <div class="pd-qty-row">
                @if ($conHang)
                    <span class="pd-lbl" id="pdQtyLbl">Số lượng</span>
                    <div class="pd-qty" role="group" aria-labelledby="pdQtyLbl">
                        <button type="button" id="pdMinus" aria-label="Giảm số lượng" disabled>−</button>
                        <output id="pdQty" aria-live="polite">1</output>
                        <button type="button" id="pdPlus" aria-label="Tăng số lượng" @disabled($sp->ton_kho <= 1)>+</button>
                    </div>
                @endif
                <span class="pd-stock {{ $sp->ton_kho > 0 && $sp->ton_kho <= 5 ? 'low' : '' }} {{ $conHang ? '' : 'out' }}">
                    {{ $conHang ? 'Còn lại: ' . $sp->ton_kho . ' sản phẩm' : 'Hết hàng' }}
                </span>
            </div>

            <div class="pd-actions">
                <button class="pd-btn ghost" type="button" id="pdAddCart" @disabled(! $conHang)>Thêm vào giỏ hàng</button>
                <button class="pd-btn main" type="button" id="pdBuy" @disabled(! $conHang)>Mua hàng</button>
            </div>
            <p class="pd-added" id="pdAdded" role="status"></p>

            <div class="pd-share">
                <span>Chia sẻ:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $linkTrang }}"
                   target="_blank" rel="noopener" aria-label="Chia sẻ Facebook">f</a>
                <button type="button" id="pdCopyLink" aria-label="Sao chép liên kết để gửi qua Zalo">Z</button>
                <a href="https://twitter.com/intent/tweet?url={{ $linkTrang }}&text={{ urlencode($sp->ten) }}"
                   target="_blank" rel="noopener" aria-label="Chia sẻ X">X</a>
            </div>

            @if ($sp->mo_ta)
                <article class="pd-story">
                    <h2>Câu chuyện</h2>
                    <p>{!! nl2br(e($sp->mo_ta)) !!}</p>
                </article>
            @endif
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var add = document.getElementById('pdAddCart');
    var buy = document.getElementById('pdBuy');
    var msg = document.getElementById('pdAdded');
    var qty = document.getElementById('pdQty');
    if (!add || !buy) { return; }

    var token = document.querySelector('meta[name="csrf-token"]').content;
    var id = {{ $sp->id }};

    function post(goCart) {
        add.disabled = true;
        buy.disabled = true;

        fetch('{{ route('gio-hang.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ san_pham_id: id, so_luong: parseInt(qty.textContent, 10) || 1 })
        }).then(function (r) {
            if (r.status === 401) {
                window.location.href = '{{ url('/login') }}';
                return null;
            }
            return r.json().then(function (d) { return { ok: r.ok, d: d }; });
        }).then(function (res) {
            if (!res) { return; }
            if (!res.ok) {
                msg.textContent = res.d.message || 'Không thêm được vào giỏ hàng.';
                return;
            }
            var badge = document.getElementById('cartBadge');
            if (badge) { badge.textContent = res.d.tong; badge.style.display = ''; }
            if (goCart) {
                window.location.href = res.d.url;
                return;
            }
            msg.textContent = 'Đã thêm ' + qty.textContent + ' × ' + {!! json_encode($sp->ten) !!} + ' vào giỏ hàng.';
        }).catch(function () {
            msg.textContent = 'Có lỗi xảy ra, thử lại sau.';
        }).then(function () {
            add.disabled = false;
            buy.disabled = false;
        });
    }

    add.addEventListener('click', function (e) {
        e.stopImmediatePropagation();
        post(false);
    }, true);

    buy.addEventListener('click', function (e) {
        e.stopImmediatePropagation();
        post(true);
    }, true);
})();
</script>
@endpush

