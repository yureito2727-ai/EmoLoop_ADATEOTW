<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\GameEngineController;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', [ProductController::class, 'index'])->name('shop');

Route::get('/san-pham/{sanPham}', [ProductController::class, 'show'])->name('san-pham.show');

Route::middleware('auth')->group(function () {
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
