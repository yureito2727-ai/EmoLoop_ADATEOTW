@extends('layouts.app')

@section('title', $chuong->tieu_de . ' – Emo Loop')
@section('body_class', 'game-page')

@section('content')
<link href="{{ asset('css/game.css') }}" rel="stylesheet">

<div class="game-wrap" id="game"
     data-vao="{{ route('game.vao', $chuong) }}"
     data-anh-goc="{{ asset('storage') }}">
    <div class="game-top">
        <span class="game-title">{{ $chuong->tieu_de }}</span>
        <div class="game-diem">
            <span>Hỷ <b id="d-hy">0</b></span>
            <span>Nộ <b id="d-no">0</b></span>
            <span>Ái <b id="d-ai">0</b></span>
            <span>Ố <b id="d-o">0</b></span>
        </div>
    </div>

    <div class="game-stage">
        <img id="anh-nv" alt="Nhân vật">
    </div>

    <div class="game-box">
        <div class="game-name" id="ten-nv"></div>
        <p id="loi-thoai">Đang tải…</p>
        <div id="lua-chon" class="game-choices"></div>
    </div>
</div>
<section id="tong-ket" class="tk-page" data-thoat="{{ url('/') }}" hidden>
    <div class="tk-head">
        <button type="button" class="tk-back" id="tk-back" aria-label="Thoát">
            <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </button>
        <div class="tk-title">
            <h1>Tổng kết</h1>
            <p id="tk-chuong"></p>
        </div>
        <button type="button" class="tk-menu" id="tk-menu">Menu</button>
    </div>

    <div class="tk-card">
        <div>
            <h2>Điểm cảm xúc</h2>
            <div class="tk-chart" id="tk-chart" role="img" aria-label="Biểu đồ cột điểm bốn cảm xúc"></div>
            <div class="tk-labels" id="tk-labels" aria-hidden="true"></div>
        </div>
        <div class="tk-stats">
            <div class="tk-row"><span>Số lần thay đổi biểu cảm</span><b id="tk-doi">0</b></div>
            <div class="tk-row"><span>Số biểu cảm đã gặp</span><b id="tk-gap">0</b></div>
            <div class="tk-row"><span>Vật phẩm nhận</span><div class="tk-tags" id="tk-vp"></div></div>
            <div class="tk-row">
                <span>Kết quả<small class="tk-need" id="tk-need"></small></span>
                <span class="tk-result" id="tk-kq"></span>
            </div>
        </div>
    </div>

    <div class="tk-actions">
        <button type="button" class="tk-btn ghost" id="tk-thoat">Thoát</button>
        <button type="button" class="tk-btn pink" id="tk-again">Chơi lại</button>
        <button type="button" class="tk-btn main" id="tk-next">Chương tiếp theo</button>
    </div>
    <div class="tk-toast" id="tk-toast" role="status"></div>
</section>

<script src="{{ asset('js/emo-engine.js') }}"></script>
@endsection