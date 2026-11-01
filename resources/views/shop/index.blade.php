@extends('layouts.app')

@section('title', 'Shop – Emo Loop')
@section('body_class', 'page-shop')

@push('styles')
    <link href="{{ asset('css/shop.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="shop-wrap">
        <a class="shop-back" href="{{ url('/') }}" aria-label="Về trang chủ" title="Về trang chủ">
        <i class="bi bi-arrow-left"></i>
        </a>

    {{-- Thanh bên: loại sản phẩm --}}
    <aside class="shop-side" aria-label="Loại sản phẩm">
        @foreach ($loais as $loai)
            <a class="shop-cat"
               href="{{ route('shop', array_merge(request()->except('loai'), ['loai' => $loai->id])) }}"
               @if ($loai->id == $loaiId) aria-current="true" @endif>
                {{ $loai->ten }}
            </a>
        @endforeach
    </aside>

    <section>
        {{-- Thanh công cụ: tìm kiếm, lọc voucher, sắp xếp --}}
        <form class="shop-toolbar" method="GET" action="{{ route('shop') }}">
            <input type="hidden" name="loai" value="{{ $loaiId }}">

            <label class="shop-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ $tuKhoa }}" placeholder="Tìm sản phẩm theo tên..." aria-label="Tìm sản phẩm">
            </label>

            <div class="shop-filters">
                <label class="shop-chip">
                    <input type="checkbox" name="voucher" value="1" @checked($coVoucher) onchange="this.form.submit()">
                    Chỉ sản phẩm có voucher
                </label>
                <label class="shop-chip">Sắp xếp
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Mới nhất</option>
                        <option value="asc" @selected($sapXep === 'asc')>Giá tăng dần</option>
                        <option value="desc" @selected($sapXep === 'desc')>Giá giảm dần</option>
                    </select>
                </label>
            </div>
        </form>

        {{-- Lưới sản phẩm --}}
        <div class="shop-grid">
            @forelse ($sanPhams as $sp)
                <article class="shop-card">
                    <a class="shop-link" href="{{ route('san-pham.show', $sp) }}">
                        <div class="shop-img">
                            @if ($sp->anh)
                                <img src="{{ asset('storage/' . $sp->anh) }}" alt="{{ $sp->ten }}" loading="lazy">
                            @else
                                Ảnh sản phẩm
                            @endif
                        </div>
                        <div class="shop-info">
                            <h3>{{ $sp->ten }}</h3>
                            <span class="shop-price">
                                {{ number_format($sp->gia, 0, ',', '.') }}đ
                                <small>· {{ $sp->ton_kho > 0 ? 'Còn ' . $sp->ton_kho : 'Hết hàng' }}</small>
                            </span>
                            @if ($sp->dong_voucher)
                                <span class="shop-voucher has">{{ $sp->dong_voucher }}</span>
                            @else
                                <span class="shop-voucher">Chưa có voucher</span>
                            @endif
                        </div>
                    </a>
                </article>
            @empty
                <p class="shop-empty">Không tìm thấy sản phẩm phù hợp.</p>
            @endforelse
        </div>
    </section>

</div>
@endsection