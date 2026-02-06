# crypto_balance_monitor

### 1. Project overvie
CryptoBalanceMonitor is a Laravel-based API application that allows users to track cryptocurrency wallet balances over time.

### 2. Supported currencies
- Bitcoin (BTC)
- Litecoin (LTC)
- Ethereum (ETH)

### 3. Architecture overview

- REST API built with Laravel
- CQRS-inspired approach for commands and queries
- Asynchronous balance updates via Laravel Queues
- Balance providers abstracted behind interfaces

### 4. Balance providers
   Balance fetching is implemented via provider interfaces.

Supported providers:
- Blockchair (BTC, LTC)
- Etherscan (ETH)

A FakeBalanceProvider is enabled by default to keep the application self-contained without external API dependencies.

### 5. External API limitations

Blockchair and Etherscan APIs require API keys and enforce strict rate limits.

To avoid external dependencies during development and testing:
- A fake balance provider is used by default
- Real providers can be enabled via environment configuration

Batch balance endpoints were considered but not implemented due to API access limitations.

### 6. Queues & cron
   Wallet balance updates are performed asynchronously.

- Cron runs every minute
- Cron dispatches jobs to queue
- Jobs fetch balances and store balance history

### 7. Testing

The project includes:
- Unit tests for services and providers
- Job tests for async balance updates
- Feature tests for API endpoints

Tests can be run using:
```shell
php artisan test
```

### 8. Static analysis & code quality

- PHP_CodeSniffer
```shell
./vendor/bin/phpcs
```
- Psalm
```shell
./vendor/bin/psalm --no-cache
```

### 9. API documentation

OpenAPI specification is available in `docs/openapi.yaml`.

The specification can be viewed using Swagger Editor or any OpenAPI-compatible tool.

### 10. Setup
```shell
cp .env.example .env
docker compose up -d --build
php artisan migrate
php artisan horizon
php artisan schedule:work
```
