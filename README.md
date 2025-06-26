
## Prerequisities
- php
- composer
- node

## Startup
Run:
 
`php run serve` to start laravel backend
 
`php artisan reverb:start` to start websocket server
 
`npm run dev` to start frontend

Or:
`make start`

app should start at http://127.0.0.1:8000

## Api usage
GET `/api/currencies` - returns list of crypto currencies
query params:
  - vs_currency 
     - refer to https://docs.coingecko.com/reference/simple-supported-currencies
     - default: usd
 - order 
	 - refer to https://docs.coingecko.com/reference/coins-markets
	 - default: market_cap_desc
- per_page
	 - items per page
	 - 1-250
	 - default: 100
 - page
	 - page through results
     - default: 1

POST `/api/currencies` - sends currency data to websocket server

expects array of 

	{
		id: string; 
		name: string; 
		symbol: string; 
		image: string; 
		current_price: number; 
		market_cap: number; 
		price_change_percentage_1h_in_currency: number; 
		price_change_percentage_24h_in_currency: number; 
		price_change_percentage_7d_in_currency: number; 
		circulating_supply: number; 
	}
  

  


