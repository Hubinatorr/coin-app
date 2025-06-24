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
            'percent_change_24h' => $this['price_change_percentage_24h'],
            'market_cap' => $this['market_cap'],
        ];
    }
}
