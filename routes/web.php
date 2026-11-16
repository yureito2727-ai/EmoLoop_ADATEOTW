<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\GameEngineController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\LoaiSanPhamController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\TaiKhoanDonHangController;
use App\Http\Controllers\ChuyenTaiKhoanController;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', [ProductController::class, 'index'])->name('shop');

Route::get('/san-pham/{sanPham}', [ProductController::class, 'show'])->name('san-pham.show');

Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [TaiKhoanController::class, 'hoSo'])->name('tai-khoan.ho-so');
    Route::get('/tai-khoan/ma-giam', [TaiKhoanController::class, 'maGiam'])->name('tai-khoan.ma-giam');
    Route::get('/tai-khoan/mat-khau', [TaiKhoanController::class, 'matKhau'])->name('tai-khoan.mat-khau.form');
    Route::patch('/tai-khoan', [ProfileController::class, 'update'])->name('tai-khoan.update');
    Route::put('/tai-khoan/mat-khau', [ProfileController::class, 'updatePassword'])->name('tai-khoan.mat-khau');
    Route::put('/tai-khoan/dia-chi', [ProfileController::class, 'updateAddress'])->name('tai-khoan.dia-chi');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [GioHangController::class, 'index'])->name('gio-hang.index');
    Route::post('/gio-hang', [GioHangController::class, 'store'])->name('gio-hang.store');
    Route::patch('/gio-hang/{gioHang}', [GioHangController::class, 'update'])->name('gio-hang.update');
    Route::delete('/gio-hang/{gioHang}', [GioHangController::class, 'destroy'])->name('gio-hang.destroy');
    Route::delete('/gio-hang', [GioHangController::class, 'clear'])->name('gio-hang.clear');
});

Route::middleware('auth')->patch('/san-pham/{sanPham}', [ProductController::class, 'update'])->name('san-pham.update');
Route::middleware('auth')->post('/game/chuong/{chuong}/vao', [GameEngineController::class, 'vaoChuong'])
    ->name('game.vao');
Route::middleware('auth')->get('/game/chuong/{chuong}', [GameEngineController::class, 'trangChoi'])
    ->name('game.choi');
Route::middleware('auth')->get('/game', [GameController::class, 'chonNhanVat'])->name('game.nhan-vat');
Route::middleware('auth')->get('/game/nhan-vat/{nhanVat}', [GameController::class, 'danhSachChuong'])->name('game.chuong-ds');


Route::middleware('auth')->group(function () {
    Route::get('/thanh-toan', [ThanhToanController::class, 'index'])->name('thanh-toan.index');
    Route::post('/thanh-toan', [ThanhToanController::class, 'store'])->name('thanh-toan.store');
    Route::get('/thanh-toan/xong/{donHang}', [ThanhToanController::class, 'xong'])->name('thanh-toan.xong');
});
/*
Route::middleware(['auth', 'admin'])->prefix('quan-tri')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.san-pham.index'))->name('home');
    Route::resource('san-pham', SanPhamController::class)->except('show')->parameters(['san-pham' => 'sanPham']);
    Route::resource('danh-muc', LoaiSanPhamController::class)->except('show')->parameters(['danh-muc' => 'loaiSanPham']);
});
*/

    Route::get('/tai-khoan/don-hang', [TaiKhoanDonHangController::class, 'index'])->name('tai-khoan.don-hang');
    Route::get('/tai-khoan/don-hang/{donHang}', [TaiKhoanDonHangController::class, 'show'])->name('tai-khoan.don-hang.show');
    Route::post('/tai-khoan/don-hang/{donHang}/huy', [TaiKhoanDonHangController::class, 'huy'])->name('tai-khoan.don-hang.huy');

        Route::post('/tai-khoan/chuyen/{user}', [ChuyenTaiKhoanController::class, 'chuyen'])->name('tai-khoan.chuyen');
    Route::post('/tai-khoan/them-tai-khoan', [ChuyenTaiKhoanController::class, 'them'])->name('tai-khoan.them-tk');
    Route::post('/tai-khoan/go-tai-khoan/{user}', [ChuyenTaiKhoanController::class, 'go'])->name('tai-khoan.go-tk');