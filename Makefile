.PHONY: init start

# Define ports
LARAVEL_PORT=8000
REVERB_PORT=6001

all: init start

init:
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
	else \
		echo ".env file exists, skipping configuration"; \
	fi
	npm i &\
    composer i;

start:
	@echo "🔍 Checking if ports are available..."
	@if lsof -i tcp:$(LARAVEL_PORT) >/dev/null 2>&1; then \
		echo "❌ Port $(LARAVEL_PORT) is already in use. Aborting."; exit 1; \
	else \
		echo "✅ Port $(LARAVEL_PORT) is free."; \
	fi
	@if lsof -i tcp:$(REVERB_PORT) >/dev/null 2>&1; then \
		echo "⚠️ Port $(REVERB_PORT) is already in use. Reverb may fail."; \
	else \
		echo "✅ Port $(REVERB_PORT) is free."; \
	fi

	@echo "🚀 Starting Laravel server, Reverb, and Vite (npm run dev)..."
	@trap 'echo "\n🛑 Stopping all..."; kill 0' SIGINT; \
	npm run dev & \
	php artisan reverb:start & \
	php artisan serve --port=$(LARAVEL_PORT) & \
	php artisan schedule:work & \
	wait
