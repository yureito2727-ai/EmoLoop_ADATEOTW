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

<script src="{{ asset('js/emo-engine.js') }}"></script>
@endsection