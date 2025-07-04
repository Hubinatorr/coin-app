<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::get('/currencies', [CurrencyController::class, 'getCurrencies']);

Route::post('/currencies', [CurrencyController::class, 'addCurrencies']);

