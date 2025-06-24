.PHONY: start serve reverb dev stop

start:
	@echo "Starting Laravel server, Reverb, and Vite dev server..."
	@$(MAKE) serve &
	@$(MAKE) reverb &
	@$(MAKE) dev &
	@wait

serve:
	@echo "Starting Laravel server..."
	php artisan serve

reverb:
	@echo "Starting Laravel Reverb..."
	php artisan reverb:start

dev:
	@echo "Starting Vite (npm run dev)..."
	npm run dev

stop:
	@echo "Stopping all processes..."
	@pkill -f "php artisan serve" || true
	@pkill -f "php artisan reverb:start" || true
	@pkill -f "vite" || pkill -f "npm run dev" || true
