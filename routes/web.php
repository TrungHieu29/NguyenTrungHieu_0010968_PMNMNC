<?php

use App\Http\Controllers\Api\LopHocController as ApiLopHocController;
use App\Http\Controllers\Api\SinhVienController as ApiSinhVienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\UserController;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'active', 'role:admin,lecturer'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/api-playground', 'api-playground')->name('api-playground');
    Route::get('/dashboard', fn () => view('dashboard', [
        'lopHocCount' => LopHoc::count(),
        'sinhVienCount' => SinhVien::count(),
        'activeLopHocCount' => LopHoc::where('trang_thai', true)->count(),
        'activeSinhVienCount' => SinhVien::where('trang_thai', true)->count(),
        'userCount' => request()->user()->role === 'admin' ? User::count() : null,
        'lecturerCount' => request()->user()->role === 'admin' ? User::where('role', 'lecturer')->count() : null,
    ]))->name('dashboard');
    Route::resource('lop-hocs', LopHocController::class);
    Route::resource('sinh-viens', SinhVienController::class);
    Route::resource('menus', MenuController::class)->middleware('role:admin');
    Route::resource('users', UserController::class)->middleware('role:admin');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/pages/{slug}', [MenuPageController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('pages.show');
});

Route::prefix('api')->middleware(['auth', 'active', 'role:admin,lecturer', 'throttle:60,1'])->name('api.')->group(function () {
    Route::apiResource('lop-hocs', ApiLopHocController::class);
    Route::apiResource('sinh-viens', ApiSinhVienController::class);
});
