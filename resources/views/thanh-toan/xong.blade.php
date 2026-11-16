@extends('layouts.app')

@section('title', 'Đặt hàng thành công – Emo Loop')
@section('body_class', 'page-thanh-toan')

@section('content')
@php
    $vnd = fn ($n) => number_format($n, 0, ',', '.') . 'đ';
@endphp
<link rel="stylesheet" href="{{ asset('css/thanh-toan.css') }}">

<div class="tt">
  <div class="tt-card tt-done">
    <h1 class="tt-title">Đặt hàng thành công</h1>
    <p class="tt-sub">Cảm ơn bạn đã mua hàng ở Emo Loop. Đơn #{{ $donHang->id }} đang chờ xác nhận.</p>

    <dl>
      <dt>Người nhận</dt>
      <dd>{{ $donHang->ten_nguoi_nhan }} · {{ $donHang->so_dien_thoai }}</dd>

      <dt>Địa chỉ</dt>
      <dd>{{ $donHang->dia_chi }}</dd>

      <dt>Thanh toán</dt>
      <dd>
        @if ($donHang->phuong_thuc_thanh_toan === 'the')
          Thẻ ••{{ $donHang->the_4_so_cuoi }}
        @else
          Thanh toán khi nhận hàng (COD)
        @endif
      </dd>

      <dt>Tổng tiền</dt>
      <dd><b>{{ $vnd($donHang->tong_tien) }}</b></dd>
    </dl>

    <div class="tt-actions">
      <a class="tt-btn" href="{{ url('/shop') }}">Tiếp tục mua sắm</a>
    </div>
  </div>
</div>
@endsection