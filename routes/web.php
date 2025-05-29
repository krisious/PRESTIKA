<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestasiPrintController;


Route::get('/', function () {
    return view('landingPage');
});

Route::get('/prestasi/print', [PrestasiPrintController::class, 'print'])->name('prestasi.print');