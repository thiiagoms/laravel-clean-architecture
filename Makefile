# Configuration
APP_CONTAINER=app
DOCKER_EXEC=docker-compose exec $(APP_CONTAINER)
DOCKER_UP=docker-compose up -d

# Default project setup
.PHONY: build
build:  copy-env docker-up composer-install artisan-setup

.PHONY: docker-up
docker-up:
	@echo "🚀 Starting Docker containers..."
	$(DOCKER_UP)

.PHONY: copy-env
copy-env:
	@if [ ! -f .env ]; then \
		echo "📄 Copying .env.example to .env..."; \
		cp .env.example .env; \
	fi

.PHONY: composer-install
composer-install:
	@echo "📦 Installing Composer dependencies..."
	$(DOCKER_EXEC) composer install -vvv

.PHONY: artisan-setup
artisan-setup:
	@echo "🔐 Generating application key..."
	$(DOCKER_EXEC) php artisan key:generate
	@echo "🔑 Generating JWT secret..."
	$(DOCKER_EXEC) php artisan jwt:secret
	@echo "📂 Running migrations..."
	$(DOCKER_EXEC) php artisan migrate

# Lint
.PHONY: lint
lint:
	@echo "🔍 Running code linting..."
	$(DOCKER_EXEC) composer pint app database tests routes

.PHONY: docs
docs:
	@echo "📚 Generating API documentation..."
	$(DOCKER_EXEC) php artisan l5-swagger:generate

# Tests
.PHONY: test
test:
	@echo "🧪 Running all tests..."
	$(DOCKER_EXEC) php artisan test

.PHONY: test-unit
test-unit:
	@echo "🧪 Running unit tests..."
	$(DOCKER_EXEC) php artisan test tests/Unit

.PHONY: test-feature
test-feature:
	@echo "🧪 Running feature tests..."
	$(DOCKER_EXEC) php artisan test tests/Feature

# Utilities
.PHONY: down
down:
	@echo "🛑 Stopping containers..."
	docker-compose down

.PHONY: logs
logs:
	@echo "📜 Tailing logs..."
	docker-compose logs -f

.PHONY: tinker
tinker:
	@echo "💡 Opening Laravel Tinker..."
	$(DOCKER_EXEC) php artisan tinker

.PHONY: migrate-refresh
migrate-refresh:
	@echo "♻️ Refreshing and seeding database..."
	$(DOCKER_EXEC) php artisan migrate:refresh --seed
