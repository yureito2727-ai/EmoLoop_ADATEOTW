@extends('layouts.app')

@section('title', 'Shop – Emo Loop')
@section('body_class', 'page-shop')

@push('styles')
    <link href="{{ asset('css/shop.css') }}" rel="stylesheet">
@endpush

@section('content')
@php $laAdmin = auth()->check() && auth()->user()->laQuanTri(); @endphp
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
                @if (session('status'))
            <div class="shop-flash" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any() && old('edit_id'))
            <div class="shop-flash err" role="alert">Chưa lưu được, xem lỗi trong cửa sổ sửa.</div>
        @endif
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
                                        @if ($laAdmin)
                        <button type="button" class="shop-edit" data-bs-toggle="modal" data-bs-target="#suaSp{{ $sp->id }}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                    @endif
                </article>
            @empty
                <p class="shop-empty">Không tìm thấy sản phẩm phù hợp.</p>
            @endforelse
        </div>
    </section>
        @if ($laAdmin)
        @foreach ($sanPhams as $sp)
            @php
                $cu = (string) old('edit_id') === (string) $sp->id;
                $v = fn ($k, $d) => $cu ? old($k, $d) : $d;
            @endphp
            <div class="modal fade sp-modal" id="suaSp{{ $sp->id }}" tabindex="-1" aria-labelledby="suaSpTitle{{ $sp->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <form class="modal-content" method="POST" action="{{ route('san-pham.update', $sp) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="edit_id" value="{{ $sp->id }}">

                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="suaSpTitle{{ $sp->id }}">Sửa sản phẩm</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>

                        <div class="modal-body">
                            @if ($cu && $errors->any())
                                <div class="shop-flash err" role="alert">
                                    @foreach ($errors->all() as $loi)
                                        <div>{{ $loi }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="row g-4">
                                <div class="col-lg-5">
                                    <label class="sp-lbl" for="spAnh{{ $sp->id }}">Ảnh đại diện</label>
                                    <div class="sp-avatar">
                                        <img id="spPrev{{ $sp->id }}" src="{{ $sp->anh ? asset('storage/' . $sp->anh) : '' }}" alt="" @if (! $sp->anh) hidden @endif>
                                        @if (! $sp->anh)
                                            <span>Chưa có ảnh</span>
                                        @endif
                                    </div>
                                    <input class="form-control" type="file" id="spAnh{{ $sp->id }}" name="anh" accept="image/*" data-sp-avatar="{{ $sp->id }}">

                                    <label class="sp-lbl mt-4">Ảnh phụ <small>(tối đa 5)</small></label>
                                    @if ($sp->anhPhu->isNotEmpty())
                                        <div class="sp-extra-grid">
                                            @foreach ($sp->anhPhu as $ap)
                                                <label class="sp-extra">
                                                    <img src="{{ asset('storage/' . $ap->duong_dan) }}" alt="">
                                                    <span>
                                                        <input type="checkbox" name="xoa_anh_phu[]" value="{{ $ap->id }}" @checked($cu && in_array($ap->id, old('xoa_anh_phu', [])))>
                                                        Xóa
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                    <input class="form-control" type="file" name="anh_phu[]" accept="image/*" multiple data-sp-extra="{{ $sp->id }}">
                                    <div class="sp-extra-grid" id="spNew{{ $sp->id }}"></div>
                                </div>

                                <div class="col-lg-7">
                                    <div class="mb-3">
                                        <label class="sp-lbl" for="spTen{{ $sp->id }}">Tên sản phẩm</label>
                                        <input class="form-control" type="text" id="spTen{{ $sp->id }}" name="ten" value="{{ $v('ten', $sp->ten) }}" required>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="sp-lbl" for="spLoai{{ $sp->id }}">Loại sản phẩm</label>
                                            <select class="form-select" id="spLoai{{ $sp->id }}" name="loai_san_pham_id" required>
                                                @foreach ($loais as $loai)
                                                    <option value="{{ $loai->id }}" @selected($v('loai_san_pham_id', $sp->loai_san_pham_id) == $loai->id)>{{ $loai->ten }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="sp-lbl" for="spGia{{ $sp->id }}">Giá (đ)</label>
                                            <input class="form-control" type="number" id="spGia{{ $sp->id }}" name="gia" min="0" step="1" value="{{ $v('gia', (int) $sp->gia) }}" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="sp-lbl" for="spMoTa{{ $sp->id }}">Mô tả</label>
                                        <textarea class="form-control" id="spMoTa{{ $sp->id }}" name="mo_ta" rows="9">{{ $v('mo_ta', $sp->mo_ta) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn sp-cancel" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn sp-save">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

</div>
@endsection
@push('scripts')
@if ($laAdmin)
<script>
(function () {
    document.querySelectorAll('input[data-sp-avatar]').forEach(function (input) {
        input.addEventListener('change', function () {
            var file = input.files[0];
            if (!file) { return; }
            var img = document.getElementById('spPrev' + input.dataset.spAvatar);
            img.src = URL.createObjectURL(file);
            img.hidden = false;
            var ph = img.parentNode.querySelector('span');
            if (ph) { ph.hidden = true; }
        });
    });

    document.querySelectorAll('input[data-sp-extra]').forEach(function (input) {
        input.addEventListener('change', function () {
            var box = document.getElementById('spNew' + input.dataset.spExtra);
            box.innerHTML = '';
            Array.prototype.forEach.call(input.files, function (file) {
                var wrap = document.createElement('div');
                wrap.className = 'sp-extra';
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = '';
                wrap.appendChild(img);
                box.appendChild(wrap);
            });
        });
    });

    @if ($errors->any() && old('edit_id'))
    var el = document.getElementById('suaSp{{ (int) old('edit_id') }}');
    if (el) { bootstrap.Modal.getOrCreateInstance(el).show(); }
    @endif
})();
</script>
@endif
@endpush