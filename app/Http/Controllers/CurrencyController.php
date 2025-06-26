<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Events\CurrencyDataUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index(): JsonResponse
    {
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
                $currencies = $response->json();
                $currencyData = CurrencyResource::collection(collect($currencies))->resolve();

                Log::info('Successfully fetched currency data.');
                return response()->json($currencyData);
            } else {
                Log::error('CoinGecko API Error: ' . $response->body());
                return response()->json([
                    'error' => 'Failed to fetch currency data from CoinGecko.',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('FetchCurrencyData Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while fetching currency data.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currencies' => 'required|array',
            'currencies.*.id' => 'required|string',
            'currencies.*.name' => 'required|string',
            'currencies.*.symbol' => 'required|string',
            'currencies.*.image' => 'required|string',
            'currencies.*.current_price' => 'required|numeric',
            'currencies.*.market_cap' => 'required|numeric',
            'currencies.*.price_change_percentage_1h_in_currency' => 'required|numeric',
            'currencies.*.price_change_percentage_24h_in_currency' => 'required|numeric',
            'currencies.*.price_change_percentage_7d_in_currency' => 'required|numeric',
            'currencies.*.circulating_supply' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid currency data.',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            $currencies = $request->input('currencies');

            $currencyData = CurrencyResource::collection(collect($currencies))->resolve();

            broadcast(new CurrencyDataUpdated($currencyData));

            return response()->json($currencyData);
        } catch (\Exception $e) {
            Log::error('Currency processing failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to process currency data.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
