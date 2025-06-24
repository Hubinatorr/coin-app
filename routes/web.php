<?php

use App\Events\CurrencyPriceUpdated;
use App\Http\Controllers\CurrencyController;
use App\Http\Resources\CurrencyResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/currencies', [CurrencyController::class, 'index']);

Route::get('/currencies-all', function () {
    try {
        // Make the API call to CoinGecko
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 1000, // Fetch top 50
            'page' => 1,
            'sparkline' => false,
            'price_change_percentage' => '1h,24h,7d'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Successfully fetched and broadcasted currency data.');
            $formattedData = CurrencyResource::collection($data)->resolve();
            return json_encode(new CurrencyPriceUpdated($formattedData));
        } else {
            Log::error('CoinGecko API Error: ' . $response->body());
        }
    } catch (\Exception $e) {
        Log::error('FetchCurrencyData Command Error: ' . $e->getMessage());
    }
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
