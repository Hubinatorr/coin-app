<?php

use App\Events\CurrencyDataUpdated;
use App\Http\Controllers\CurrencyController;
use App\Http\Resources\CurrencyResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('CoinTable');
})->name('home');

Route::get('/currencies', [CurrencyController::class, 'index']);

Route::get('/currencies-all', function () {
    try {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 100,
            'page' => 1,
            'sparkline' => false,
            'price_change_percentage' => '1h,24h,7d'
        ]);

        if ($response->successful()) {
            Log::info('Successfully fetched and broadcasted currency data.');
            broadcast(new CurrencyDataUpdated(CurrencyResource::collection($response->json())->resolve()));
        } else {
            Log::error('CoinGecko API Error: ' . $response->body());
        }
    } catch (\Exception $e) {
        Log::error('FetchCurrencyData Command Error: ' . $e->getMessage());
    }
});

Route::get('/broadcast-currencies', function () {
    broadcast(new CurrencyDataUpdated([]));
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
