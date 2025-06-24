<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\CurrencyResource;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method provides the initial data to the frontend.
     * It fetches the latest data from the cache.
     */
    public function index()
    {
        // Fetch the latest currency data from the cache, which is populated
        // by the scheduled command.
        $currencyData = Cache::get('latest_currency_data', []);

        return CurrencyResource::collection($currencyData);
    }
}
