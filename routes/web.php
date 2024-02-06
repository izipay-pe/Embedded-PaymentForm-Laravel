<?php

use App\Http\Controllers\IzipayController;
use Illuminate\Support\Facades\Route;


Route::get('/', [IzipayController::class, 'getFormToken']);

Route::post('/paid', [IzipayController::class, 'success']);
