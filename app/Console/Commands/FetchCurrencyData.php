<?php

namespace App\Console\Commands;

use App\Events\CurrencyPriceUpdated;
use App\Http\Resources\CurrencyResource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyData extends Command
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Fetching currency data from CoinGecko...');

        try {
            // Make the API call to CoinGecko
            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 50, // Fetch top 50
                'page' => 1,
                'sparkline' => false,
                'price_change_percentage' => '1h,24h,7d'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store the latest data in the cache for the API endpoint
                Cache::put('latest_currency_data', $data, now()->addMinutes(5));

                // Format the data using the resource
                $formattedData = CurrencyResource::collection($data)->resolve();

                // Broadcast the event with the new data
                broadcast(new CurrencyPriceUpdated($formattedData));
                Log::info('Successfully fetched and broadcasted currency data.');
            } else {
                Log::error('CoinGecko API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FetchCurrencyData Command Error: ' . $e->getMessage());
        }
    }
}
