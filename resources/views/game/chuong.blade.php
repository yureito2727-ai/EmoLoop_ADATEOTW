@extends('layouts.app')

@section('title', $chuong->tieu_de . ' – Emo Loop')
@section('body_class', 'game-page')

@section('content')
<link href="{{ asset('css/game.css') }}" rel="stylesheet">

<div class="gm" id="game"
     data-vao="{{ route('game.vao', $chuong) }}"
     data-anh-goc="{{ asset('storage') }}">
    <div class="gm-wrap">
        <div class="gm-top">
            <a class="gm-back" href="{{ route('game.chuong-ds', $chuong->nhan_vat_id) }}" aria-label="Quay lại danh sách chương">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            </a>
            <div class="gm-prog" id="tien-trinh" aria-label="Tiến trình chương"></div>
            <button class="gm-menu" id="menu-btn" type="button">Menu</button>
        </div>

        <div class="gm-stage">
            <section class="gm-chart" aria-label="Điểm cảm xúc">
                <h3>Điểm cảm xúc</h3>
                <div id="bieu-do"></div>
            </section>
            <div class="gm-char"><img id="anh-nv" alt="Nhân vật"></div>
            <aside class="gm-side">
                <div class="gm-choices" id="lua-chon"></div>
                <div class="gm-hint" id="goi-y" hidden>Nhấn vào khung thoại để tiếp tục</div>
            </aside>
        </div>

        <button class="gm-dlg" id="khung-thoai" type="button" disabled>
            <b id="ten-nv"></b>
            <span class="gm-say" id="loi-thoai">Đang tải…</span>
            <span class="gm-nx" id="gm-nx" hidden>Nhấn để tiếp tục ▸</span>
        </button>
    </div>

    <div class="gm-ov" id="menu-ov" role="dialog" aria-modal="true" aria-labelledby="menu-tieu-de">
        <div class="gm-mod">
            <h2 id="menu-tieu-de">Menu</h2>
            <p>Chơi lại sẽ đặt điểm về điểm khởi đầu của chương.</p>
            <div class="gm-act">
                <button class="gm-btn alt" id="menu-tiep" type="button">Tiếp tục</button>
                <button class="gm-btn" id="menu-lai" type="button">Chơi lại chương</button>
            </div>
        </div>
    </div>
</div>

<section id="tong-ket" class="tk-page" data-thoat="{{ route('game.nhan-vat') }}" data-ds-chuong="{{ route('game.chuong-ds', $chuong->nhan_vat_id) }}" hidden>
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
