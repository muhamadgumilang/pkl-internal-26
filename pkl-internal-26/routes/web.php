<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ================================================
// TUGAS: Tambahkan route baru di bawah ini
// ================================================

Route::get('/tentang', function () {
    // ================================================
    // Route::get() = Tangani HTTP GET request
    // '/tentang'   = URL yang akan dihandle
    // function     = Kode yang dijalankan saat URL diakses
    // ================================================

    return view('tentang');
    // ↑ return view('tentang') = Tampilkan file tentang.blade.php
    // ↑ Laravel akan mencari di: resources/views/tentang.blade.php
});