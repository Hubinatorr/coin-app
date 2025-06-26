<?php

namespace App\Console\Commands;

use App\Events\CurrencyDataUpdated;
use App\Http\Resources\CurrencyResource;
use Illuminate\Console\Command;
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
        $params = [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 100,
            'page' => 1,
            'price_change_percentage' => '1h,24h,7d'
        ];

        try {
            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', $params);

            if ($response->successful()) {
                $data = $response->json();
                $formattedData = CurrencyResource::collection($data)->resolve();
                broadcast(new CurrencyDataUpdated($formattedData));
                Log::info('Successfully fetched and broadcasted currency data.');
            } else {
                Log::error('CoinGecko API Error: ' . $response->body(), [
                    'status' => $response->status(),
                    'request_params' => $params,
                    'coingecko_response' => $response->json()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred while fetching currency data: ' . $e->getMessage(), [
                'exception' => $e,
                'request_params' => $params
            ]);
        }
    }
}
