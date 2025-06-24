<?php

namespace App\Console\Commands;

use App\Events\CurrencyDataUpdated;
use App\Http\Resources\CurrencyResource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-currency-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and broadcast real-time currency data from CoinGecko';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 100,
            'page' => 1,
            'sparkline' => false,
            'price_change_percentage' => '1h,24h,7d'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Cache::put('latest_currency_data', $data, now()->addMinutes(5));

            $formattedData = CurrencyResource::collection($data)->resolve();
            Log::info('Successfully fetched and broadcast currency data.');
            broadcast(new CurrencyDataUpdated($formattedData));
        } else {
            Log::error('CoinGecko API Error: ' . $response->body());
        }





    }
}
