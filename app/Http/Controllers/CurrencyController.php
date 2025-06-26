<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Events\CurrencyDataUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CurrencyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $rules = [
                'vs_currency' => 'sometimes|in:usd,eur,gbp',
                'order' => 'sometimes|string',
                'per_page' => 'sometimes|integer|min:1|max:250',
                'page' => 'sometimes|integer|min:1',
                'price_change_percentage' => 'sometimes|string',
            ];

            $messages = [
                'vs_currency.in' => 'The selected currency for "vs_currency" is invalid. Allowed values are usd, eur, gbp.',
                'per_page.integer' => 'The "per_page" parameter must be an integer.',
                'per_page.min' => 'The "per_page" value must be at least 1.',
                'per_page.max' => 'The "per_page" value cannot exceed 250.',
                'page.integer' => 'The "page" parameter must be an integer.',
                'page.min' => 'The "page" value must be at least 1.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                Log::warning('Validation failed for CoinGecko API request.', [
                    'errors' => $validator->errors()->all(),
                    'request_input' => $request->all()
                ]);
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedQueryParams = $validator->validated();

            $defaultParams = [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 100,
                'page' => 1,
                'price_change_percentage' => '1h,24h,7d'
            ];

            $params = array_merge($defaultParams, $validatedQueryParams);

            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', $params);

            if ($response->successful()) {
                $currencies = $response->json();
                $currencyData = CurrencyResource::collection(collect($currencies))->resolve();
                Log::info('Successfully fetched currency data from CoinGecko.');
                return response()->json($currencyData, ResponseAlias::HTTP_OK);
            } else {
                Log::error('CoinGecko API Error: ' . $response->body(), [
                    'status' => $response->status(),
                    'request_params' => $params,
                    'coingecko_response' => $response->json()
                ]);

                return response()->json([
                    'error' => 'Failed to fetch currency data from CoinGecko.',
                    'details' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred while fetching currency data: ' . $e->getMessage(), [
                'exception' => $e,
                'request_params' => $request->all()
            ]);

            return response()->json([
                'error' => 'An unexpected server error occurred.',
                'message' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500
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
