@extends('layouts.app')

@section('title', 'Emo Loop')

@section('content')
<section class="hero" style="background-image: linear-gradient(rgba(0,0,0,.28), rgba(0,0,0,.38)), url('{{ asset('images/home-bg.jpg') }}');">
    <div class="hero-content">
        <h1 class="hero-title">Emo Loop</h1>
        <p class="hero-tagline">Chơi game, nhận mã giảm giá cho đồ lưu niệm của chính bạn.</p>
        <a href="{{ url('/play') }}" class="btn-play">Play</a>
    </div>
</section>
@endsection