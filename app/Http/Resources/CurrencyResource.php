<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'symbol' => $this['symbol'],
            'icon' => $this['image'],
            'current_price' => $this['current_price'],
            'market_cap' => $this['market_cap'],
            'price_change_percentage_1h_in_currency' => $this['price_change_percentage_1h_in_currency'],
            'price_change_percentage_24h_in_currency' => $this['price_change_percentage_24h_in_currency'],
            'price_change_percentage_7d_in_currency' => $this['price_change_percentage_7d_in_currency'],
            'circulating_supply' => $this['circulating_supply'],
        ];
    }
}
