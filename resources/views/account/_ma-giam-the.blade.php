@php
    $phanTram = in_array($m->loai_giam, ['phan_tram', '%'], true);
    $nhan = $phanTram
        ? '-' . (float) $m->gia_tri . '%'
        : '-' . number_format((float) $m->gia_tri, 0, ',', '.') . 'đ';
    $nguon = $m->nhanVat?->ten ?? $m->vatPham?->ten;
@endphp
<div class="acct-coupon {{ $tt }}">
    <div class="acct-coupon-value">{{ $nhan }}</div>
    <div class="acct-coupon-body">
        <strong class="acct-coupon-code">{{ $m->ma }}</strong>
        <span>
            @if ($m->sanPham)
                Áp dụng cho <a href="{{ route('san-pham.show', $m->san_pham_id) }}">{{ $m->sanPham->ten }}</a>
            @else
                Áp dụng cho sản phẩm được chỉ định
            @endif
        </span>
        @if ($nguon)
            <span>Nhận từ game: {{ $nguon }}</span>
        @endif
        <small>
            @if ($tt === 'da-dung' && $m->pivot->ngay_su_dung)
                Đã dùng ngày {{ \Carbon\Carbon::parse($m->pivot->ngay_su_dung)->format('d/m/Y') }}
            @elseif ($m->ngay_het_han)
                {{ $tt === 'het-han' ? 'Hết hạn' : 'Hạn dùng' }} {{ $m->ngay_het_han->format('d/m/Y') }}
            @else
                Không giới hạn thời gian
            @endif
        </small>
    </div>
</div>
