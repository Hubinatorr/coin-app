.PHONY: start

start:
	@echo "Starting all services in parallel..."
	@$(MAKE) serve &
	@$(MAKE) dev &
	@$(MAKE) schedule &
	@$(MAKE) reverb &
	@wait

serve:
	@echo "Starting PHP Artisan Serve..."
	php artisan serve

dev:
	@echo "Starting NPM Dev Server..."
	npm run dev

schedule:
	@echo "Starting Laravel Scheduler..."
	php artisan schedule:work

reverb:
	@echo "Starting Laravel Reverb..."
	php artisan reverb:start
