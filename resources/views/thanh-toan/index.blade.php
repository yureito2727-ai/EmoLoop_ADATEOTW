@extends('layouts.app')

@section('title', 'Thanh toán – Emo Loop')

@section('content')
@php
    $vnd = fn ($n) => number_format($n, 0, ',', '.') . 'đ';
    $dong = $tinh['dong'];
    $ten = old('ten_nguoi_nhan', $user->name);
    $sdt = old('so_dien_thoai', $user->so_dien_thoai);
    $diaChi = old('dia_chi', '');
    $pt = old('phuong_thuc', 'cod');
    $suaDiaChi = $diaChi === '' || $errors->hasAny(['ten_nguoi_nhan', 'so_dien_thoai', 'dia_chi']);
@endphp
<link rel="stylesheet" href="{{ asset('css/thanh-toan.css') }}">

<div class="tt">
  <h1 class="tt-title">Thanh toán</h1>
  <p class="tt-sub">Kiểm tra địa chỉ, sản phẩm và phương thức thanh toán trước khi đặt hàng.</p>

  @if ($errors->any())
    <div class="tt-card tt-err" role="alert">
      <ul>
        @foreach ($errors->all() as $loi)
          <li>{{ $loi }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="ttForm" method="POST" action="{{ route('thanh-toan.store') }}" novalidate>
    @csrf
    @foreach ($dong as $d)
      <input type="hidden" name="chon[]" value="{{ $d['id'] }}">
    @endforeach
    <input type="hidden" name="the_4_so_cuoi" id="the4" value="">

    <div class="tt-stack">
      <section class="tt-card" aria-label="Địa chỉ nhận hàng">
        <h2 class="tt-h2">Địa chỉ nhận hàng</h2>
        <div class="tt-addr{{ $suaDiaChi ? ' is-edit' : '' }}" id="addr">
          <div class="tt-view">
            <div class="tt-who"><b id="vTen">{{ $ten }}</b><span id="vSdt">{{ $sdt }}</span></div>
            <div class="tt-where" id="vDc">{{ $diaChi !== '' ? $diaChi : 'Chưa có địa chỉ. Bấm “Thêm / đổi” để nhập.' }}</div>
          </div>
          <div class="tt-fields">
            <div class="tt-field">
              <label for="fTen">Họ và tên người nhận</label>
              <input id="fTen" name="ten_nguoi_nhan" type="text" value="{{ $ten }}" autocomplete="name">
            </div>
            <div class="tt-field">
              <label for="fSdt">Số điện thoại</label>
              <input id="fSdt" name="so_dien_thoai" type="tel" value="{{ $sdt }}" autocomplete="tel">
            </div>
            <div class="tt-field tt-wide">
              <label for="fDc">Địa chỉ nhận hàng</label>
              <input id="fDc" name="dia_chi" type="text" value="{{ $diaChi }}" placeholder="Số nhà, đường, phường, quận, tỉnh/thành" autocomplete="street-address">
            </div>
          </div>
          <button class="tt-ghost" id="toggleAddr" type="button">{{ $suaDiaChi ? 'Xong' : 'Thêm / đổi' }}</button>
        </div>
      </section>

      <section class="tt-card" aria-label="Sản phẩm">
        <h2 class="tt-h2">Sản phẩm</h2>
        <div class="tt-list">
          @foreach ($dong as $d)
            <article class="tt-item">
              <div class="tt-prod">
                <div class="tt-thumb">
                  @if ($d['anh'])
                    <img src="{{ asset('storage/' . $d['anh']) }}" alt="{{ $d['ten'] }}">
                  @else
                    Ảnh
                  @endif
                </div>
                <div>
                  <a class="tt-name" href="{{ $d['url'] }}">{{ $d['ten'] }}</a>
                  @if ($d['ma'])
                    <div class="tt-code">Mã giảm: {{ $d['ma'] }} ({{ $d['nhan'] }})</div>
                  @else
                    <div class="tt-code none">Chưa áp mã giảm</div>
                  @endif
                </div>
              </div>
              <div class="tt-price">{{ $vnd($d['gia']) }}</div>
              <div class="tt-q">x{{ $d['so_luong'] }}</div>
              <div class="tt-total">
                @if ($d['giam'] > 0)
                  <span class="old">{{ $vnd($d['thanh_tien']) }}</span>
                @endif
                <span class="now">{{ $vnd($d['thanh_toan']) }}</span>
              </div>
            </article>
          @endforeach
        </div>
      </section>

      <section class="tt-card" aria-label="Vận chuyển">
        <div class="tt-ship">
          <div class="tt-info">
            <span>Ngày nhận hàng dự kiến: <b>{{ $giaoTu->format('d/m') }} – {{ $giaoDen->format('d/m/Y') }}</b></span>
            <span>Chi phí vận chuyển: <b>{{ $vnd($tinh['phi']) }}</b></span>
          </div>
          <div class="tt-ship-right">
            @if ($tinh['giam_ship'] > 0)
              <span class="tt-chip">Voucher vận chuyển: −{{ $vnd($tinh['giam_ship']) }}</span>
            @endif
            <div class="tt-subtotal">Tổng tiền ({{ $tinh['so_dong'] }} sản phẩm): <b>{{ $vnd($tinh['hang_sau_giam']) }}</b></div>
          </div>
        </div>
      </section>

      <section class="tt-card" aria-label="Phương thức thanh toán">
        <div class="tt-pay">
          <h2 class="tt-h2">Phương thức thanh toán</h2>
          <div class="tt-cur">
            <span id="payName">{{ $pt === 'the' ? 'Thanh toán bằng thẻ' : 'Thanh toán khi nhận hàng (COD)' }}</span>
            <button class="tt-ghost" id="changePay" type="button">Đổi</button>
          </div>
        </div>
        <div class="tt-opts{{ $pt === 'the' ? ' show' : '' }}" id="opts">
          <label class="tt-opt"><input type="radio" name="phuong_thuc" value="cod" @checked($pt !== 'the')> Thanh toán khi nhận hàng (COD)</label>
          <label class="tt-opt"><input type="radio" name="phuong_thuc" value="the" @checked($pt === 'the')> Thanh toán bằng thẻ</label>
        </div>
        <div class="tt-cardbox{{ $pt === 'the' ? ' show' : '' }}" id="cardbox">
          <label for="cardNo">Số thẻ</label>
          <input id="cardNo" type="text" inputmode="numeric" placeholder="1234 5678 9012 3456" autocomplete="cc-number" maxlength="23">
          <span class="tt-msg">Nhập số thẻ gồm 13–19 chữ số.</span>
          <small class="tt-hint">Hệ thống chỉ lưu 4 số cuối của thẻ.</small>
        </div>
      </section>

      <section class="tt-card tt-sumcard" aria-label="Tổng thanh toán">
        <div class="tt-sumbox">
          <div class="tt-row"><span>Số tiền hàng</span><span>{{ $vnd($tinh['hang']) }}</span></div>
          <div class="tt-row"><span>Tiền vận chuyển</span><span>{{ $vnd($tinh['phi']) }}</span></div>
          <div class="tt-row"><span>Ưu đãi và voucher</span><span class="disc">−{{ $vnd($tinh['tien_giam']) }}</span></div>
          <div class="tt-grand"><span>Tổng</span><b>{{ $vnd($tinh['tong']) }}</b></div>
          <button class="tt-btn" id="order" type="submit">Đặt hàng</button>
        </div>
      </section>
    </div>
  </form>
</div>

<script>
(function(){
  var form=document.getElementById('ttForm');
  var addr=document.getElementById('addr');
  var toggle=document.getElementById('toggleAddr');
  var fTen=document.getElementById('fTen'),fSdt=document.getElementById('fSdt'),fDc=document.getElementById('fDc');
  var opts=document.getElementById('opts'),cardbox=document.getElementById('cardbox');
  var cardNo=document.getElementById('cardNo'),the4=document.getElementById('the4');
  var payName=document.getElementById('payName');

  toggle.addEventListener('click',function(){
    var editing=addr.classList.toggle('is-edit');
    toggle.textContent=editing?'Xong':'Thêm / đổi';
    if(!editing){
      document.getElementById('vTen').textContent=fTen.value;
      document.getElementById('vSdt').textContent=fSdt.value;
      document.getElementById('vDc').textContent=fDc.value||'Chưa có địa chỉ. Bấm “Thêm / đổi” để nhập.';
    }
  });

  document.getElementById('changePay').addEventListener('click',function(){opts.classList.toggle('show')});

  document.querySelectorAll('input[name=phuong_thuc]').forEach(function(r){
    r.addEventListener('change',function(){
      var the=document.querySelector('input[name=phuong_thuc]:checked').value==='the';
      cardbox.classList.toggle('show',the);
      cardbox.classList.remove('invalid');
      payName.textContent=the?'Thanh toán bằng thẻ':'Thanh toán khi nhận hàng (COD)';
    });
  });

  cardNo.addEventListener('input',function(){
    var v=this.value.replace(/\D/g,'').slice(0,19);
    this.value=v.replace(/(.{4})/g,'$1 ').trim();
  });

  form.addEventListener('submit',function(e){
    var the=document.querySelector('input[name=phuong_thuc]:checked').value==='the';
    the4.value='';
    if(the){
      var digits=cardNo.value.replace(/\D/g,'');
      if(digits.length<13||digits.length>19){
        e.preventDefault();
        cardbox.classList.add('invalid');
        cardNo.focus();
        return;
      }
      the4.value=digits.slice(-4);
    }
    document.getElementById('order').disabled=true;
  });
})();
</script>
@endsection
