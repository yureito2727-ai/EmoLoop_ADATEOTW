@extends('layouts.app')

@section('title', 'Chọn nhân vật – Emo Loop')
@section('body_class', 'page-game')

@section('content')
<div class="cn-wrap">
    <div class="cn-head">
        <a class="cn-back" href="{{ url('/') }}" aria-label="Quay lại màn hình chính">
            <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="cn-title">Emo Loop</h1>
        <span></span>
    </div>

    <section class="cn-card" aria-label="Chọn nhân vật">
        <h2>Chọn nhân vật</h2>
        <div class="cn-stage">
            <button class="cn-arrow" id="cnPrev" type="button" aria-label="Nhân vật trước">
                <svg viewBox="0 0 36 36"><defs><linearGradient id="gp" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff5fc2"/><stop offset="1" stop-color="#ffdd57"/></linearGradient></defs><path d="M28 5 L8 18 L28 31 Z" fill="url(#gp)" stroke="url(#gp)" stroke-width="3" stroke-linejoin="round"/></svg>
            </button>
            <div class="cn-track" id="cnTrack">
                @forelse ($nhanVats as $nv)
                    <a class="cn-char" href="{{ route('game.chuong-ds', $nv) }}" aria-label="Chọn {{ $nv->ten }}">
                        @if ($nv->anh)
                            <img src="{{ asset('storage/'.$nv->anh) }}" alt="{{ $nv->ten }}">
                        @else
                            <div class="cn-ph">Ảnh nhân vật</div>
                        @endif
                        <div class="cn-label">
                            <b>{{ $nv->ten }}</b>
                            @if ($nv->mo_ta)<span>{{ $nv->mo_ta }}</span>@endif
                        </div>
                    </a>
                @empty
                    <p class="cn-empty">Chưa có nhân vật nào.</p>
                @endforelse
            </div>
            <button class="cn-arrow" id="cnNext" type="button" aria-label="Nhân vật sau">
                <svg viewBox="0 0 36 36"><defs><linearGradient id="gn" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff5fc2"/><stop offset="1" stop-color="#ffdd57"/></linearGradient></defs><path d="M8 5 L28 18 L8 31 Z" fill="url(#gn)" stroke="url(#gn)" stroke-width="3" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var track = document.getElementById('cnTrack');
    var prev = document.getElementById('cnPrev');
    var next = document.getElementById('cnNext');

    function sync() {
        var max = track.scrollWidth - track.clientWidth;
        prev.disabled = track.scrollLeft <= 2;
        next.disabled = track.scrollLeft >= max - 2;
        var hide = max <= 2 ? 'hidden' : '';
        prev.style.visibility = hide;
        next.style.visibility = hide;
    }

    function step(d) {
        var c = track.querySelector('.cn-char');
        if (!c) return;
        var gap = parseFloat(getComputedStyle(track).columnGap || 0);
        track.scrollBy({ left: d * (c.offsetWidth + gap) });
    }

    prev.addEventListener('click', function () { step(-1); });
    next.addEventListener('click', function () { step(1); });
    track.addEventListener('scroll', sync);
    window.addEventListener('resize', sync);
    sync();
})();
</script>
@endpush