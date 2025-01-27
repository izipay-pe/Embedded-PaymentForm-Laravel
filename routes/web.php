<?php

use App\Http\Controllers\IzipayController;
use Illuminate\Support\Facades\Route;

Route::get('', [IzipayController::class, 'index'])->name("index");
Route::post('checkout', [IzipayController::class, 'checkout'])->name("checkout");
Route::post("result", [IzipayController::class, 'result'])->name("result");
