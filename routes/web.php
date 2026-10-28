<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::middleware('auth')->group(function () {
    Route::patch('/tai-khoan', [ProfileController::class, 'update'])->name('tai-khoan.update');
    Route::put('/tai-khoan/mat-khau', [ProfileController::class, 'updatePassword'])->name('tai-khoan.mat-khau');
});

require __DIR__.'/auth.php';