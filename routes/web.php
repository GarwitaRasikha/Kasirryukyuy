<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/fitur', [SiteController::class, 'fitur'])->name('fitur');
Route::get('/tentang', [SiteController::class, 'tentang'])->name('tentang');
Route::get('/kontak', [SiteController::class, 'kontak'])->name('kontak');
Route::get('/panduan', [SiteController::class, 'panduan'])->name('panduan');
Route::get('/faq', [SiteController::class, 'faq'])->name('faq');
Route::post('/kontak', [SiteController::class, 'kirimKontak'])->name('kirim-kontak');
Route::get('/login', [SiteController::class, 'loginForm'])->name('login');
Route::post('/login', [SiteController::class, 'authenticate'])->name('authenticate');
Route::get('/register', [SiteController::class, 'registerForm'])->name('register');
Route::post('/register', [SiteController::class, 'registerStore'])->name('register.store');
Route::get('/dashboard', [SiteController::class, 'dashboard'])->name('dashboard');
Route::post('/dashboard', [SiteController::class, 'dashboard']);
Route::get('/logout', [SiteController::class, 'logout'])->name('logout');
