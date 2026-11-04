@extends('layouts.app')

@section('title', 'Chọn chương – Emo Loop')
@section('body_class', 'page-game')

@section('content')
<div class="cn-wrap">
    <div class="cn-head">
        <a class="cn-back" href="{{ route('game.nhan-vat') }}" aria-label="Quay lại chọn nhân vật">
            <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="cn-title">{{ $nhanVat->ten }}</h1>
        <span></span>
    </div>

    <section class="cn-card" aria-label="Danh sách chương">
        <h2>Chọn chương</h2>

        <div class="cc-list">
            @forelse ($chuongs as $chuong)
                @php
                    $khoa = $chuong->thu_tu > $xaNhat;
                    $dangMo = $chuong->thu_tu === $xaNhat;
                @endphp
                <article class="cc-item {{ $khoa ? 'is-locked' : '' }} {{ $dangMo ? 'is-current' : '' }}">
                    <div class="cc-num">{{ $chuong->thu_tu }}</div>
                    <div class="cc-info">
                        <b>{{ $chuong->tieu_de }}</b>
                        <span class="cc-status">
                            @if ($khoa)
                                <i class="bi bi-lock-fill"></i> Chưa mở khóa
                            @elseif ($dangMo)
                                Đang mở
                            @else
                                Đã qua
                            @endif
                        </span>
                    </div>
                    @if ($khoa)
                        <button class="cc-btn" type="button" disabled>Khóa</button>
                    @else
                        <a class="cc-btn" href="{{ route('game.choi', $chuong) }}">{{ $dangMo ? 'Vào chơi' : 'Chơi lại' }}</a>
                    @endif
                </article>
            @empty
                <p class="cn-empty">Chưa có chương nào.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection