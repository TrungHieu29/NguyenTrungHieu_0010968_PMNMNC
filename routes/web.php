<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\LopHocController as ApiLopHocController;
use App\Http\Controllers\Api\SinhVienController as ApiSinhVienController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuPageController;
use App\Http\Controllers\SinhVienController;
use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/api-playground', 'api-playground')->name('api-playground');
    Route::get('/dashboard', fn () => view('dashboard', [
        'lopHocCount' => LopHoc::count(),
        'sinhVienCount' => SinhVien::count(),
        'activeLopHocCount' => LopHoc::where('trang_thai', true)->count(),
        'activeSinhVienCount' => SinhVien::where('trang_thai', true)->count(),
    ]))->name('dashboard');
    Route::resource('lop-hocs', LopHocController::class)->except('destroy');
    Route::delete('lop-hocs/{lop_hoc}', [LopHocController::class, 'destroy'])->middleware('role:admin')->name('lop-hocs.destroy');
    Route::resource('sinh-viens', SinhVienController::class)->except('destroy');
    Route::delete('sinh-viens/{sinh_vien}', [SinhVienController::class, 'destroy'])->middleware('role:admin')->name('sinh-viens.destroy');
    Route::resource('menus', MenuController::class)->middleware('role:admin');
    Route::get('/pages/{slug}', [MenuPageController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('pages.show');
});

// The API uses the same session and CSRF middleware as the Blade pages.
Route::prefix('api')->middleware(['auth', 'throttle:60,1'])->name('api.')->group(function () {
    Route::apiResource('lop-hocs', ApiLopHocController::class)->except('destroy');
    Route::delete('lop-hocs/{lop_hoc}', [ApiLopHocController::class, 'destroy'])->middleware('role:admin')->name('lop-hocs.destroy');
    Route::apiResource('sinh-viens', ApiSinhVienController::class)->except('destroy');
    Route::delete('sinh-viens/{sinh_vien}', [ApiSinhVienController::class, 'destroy'])->middleware('role:admin')->name('sinh-viens.destroy');
});
