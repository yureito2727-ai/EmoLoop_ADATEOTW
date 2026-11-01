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
    <script src="{{ asset('js/san-pham.js') }}"></script>
@endpush
