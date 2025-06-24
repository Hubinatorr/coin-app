<?php

use Illuminate\Http\Request;
use App\Events\CurrencyDataUpdated;
use App\Http\Resources\CurrencyResource;
use Illuminate\Support\Facades\Route;

Route::post('/trigger-paginated-currency-update', function (Request $request) {
    $currencies = $request->input('currencies');
    $currencyData = CurrencyResource::collection($currencies)->resolve();
    broadcast(new CurrencyDataUpdated($currencyData));
    return response()->json($currencies);
});

