# crypto_balance_monitor

## 1. Project overview

CryptoBalanceMonitor is a Laravel-based REST API application for tracking cryptocurrency wallet balances over time.

The application allows users to:
- add wallets for monitoring,
- retrieve a list of tracked wallets,
- fetch current balance for a specific wallet,
- asynchronously update wallet balances and store balance history.

Balance updates are performed in background to avoid blocking API requests and to respect external provider limitations.

---

## 2. Supported currencies

The application supports the following cryptocurrencies:
- Bitcoin (BTC)
- Litecoin (LTC)
- Ethereum (ETH)

Supported currencies are modeled as a domain enum to ensure consistency across validation, persistence, and integrations.

---

## 3. Architecture overview

The project is designed with focus on modularity, extensibility, and explicit domain modeling.

Key architectural decisions:
- REST API built with Laravel
- CQRS-inspired approach for separating write and read operations
- Explicit domain layer for wallet-related business logic
- Asynchronous balance updates via Laravel Queues
- Balance providers abstracted behind interfaces

Business logic is encapsulated in command/query handlers and reused across API controllers, jobs, and cron tasks.

---

## 4. Wallet lifecycle & statuses

Each wallet has an explicit lifecycle represented by a status field.

Statuses are stored in the database as a string (for flexibility), but are modeled as an enum (`WalletStatus`) in the codebase.

Available statuses:
- `created` — wallet has been added but balance has not been synced yet
- `syncing` — balance update is currently in progress
- `active` — balance successfully synced and up to date
- `failed` — balance update failed due to an external or internal error

Wallet status is used to:
- control which wallets are selected for cron-based updates
- avoid duplicate or concurrent balance updates
- transparently expose wallet state via the API
- centralize lifecycle transitions in domain handlers

---

## 5. CQRS approach

Write and read operations are separated using a CQRS-inspired structure:

- **Commands** handle state changes (e.g. CreateWallet, UpdateWalletBalance)
- **Queries** handle read-only operations (e.g. GetWalletById, GetWallets)

Both wallet creation and balance update logic are implemented as domain handlers, allowing consistent behavior across:
- HTTP API
- queue jobs
- cron execution

---

## 6. Balance providers

Balance fetching is implemented via a provider interface to allow multiple external integrations.

Supported providers:
- Blockchair (BTC, LTC)
- Etherscan (ETH)

A `FakeBalanceProvider` is enabled by default to keep the application self-contained and runnable without external API dependencies.

Real providers can be enabled via environment configuration.

---

## 7. External API limitations & batch requests

Blockchair and Etherscan APIs require API keys and enforce rate limits.

To simplify local development and testing:
- external API calls are optional
- fake provider is used by default

Batch balance endpoints were considered but intentionally not implemented:
- limited access to production API keys
- difficulty reproducing batch scenarios locally
- application context does not assume a very large number of wallets per user

The current architecture allows batch processing to be added later by introducing a new provider implementation without changing domain logic.

---

## 8. Queues & cron

Wallet balance updates are performed asynchronously.

Flow:
- Cron runs every minute
- Cron selects wallets with updatable statuses (`created`, `active`)
- For each wallet, a queue job is dispatched
- Job invokes the domain handler to update balance and store history

Jobs act as a thin orchestration layer and do not contain business logic.

---

## 9. Validation

- Currency is validated using a domain enum
- Wallet addresses are validated via a custom validation rule
- No synchronous external API calls are made during wallet creation

Actual balance synchronization and validation of address existence happen asynchronously during balance updates.

---

## 10. Testing

The project includes:
- Unit tests for domain logic (handlers, enums)
- Feature tests for API endpoints and HTTP contracts

Jobs and cron tasks are not tested directly, as they only orchestrate execution of domain use-cases.

Tests can be run with:
```shell
php artisan test
```

---

### 11. Static analysis & code quality
The project uses the following tools to ensure code quality:
- PHP_CodeSniffer
```shell
./vendor/bin/phpcs
```
- Psalm
```shell
./vendor/bin/psalm --no-cache
```
The codebase uses strict typing, typed properties, and enums for domain values.

---

### 12. API documentation

OpenAPI specification is available in `docs/openapi.yaml`.

The specification can be viewed using Swagger Editor or any OpenAPI-compatible tool.

---

### 13. Setup
```shell
cp .env.example .env
docker compose up -d --build
php artisan migrate
php artisan horizon
php artisan schedule:work
```

---

### 14. Design decisions & trade-offs
Key design decisions made during development:
- wallet statuses are stored as strings in DB but modeled as enums in code
- external API calls are asynchronous to keep API responsive
- batch balance updates are not implemented due to API and context constraints
- domain logic is isolated from framework and infrastructure details

The architecture is designed to allow future extensions such as:
- adding new external providers
- additional currencies
- batch balance updates
- alerts and analytics based on balance changes
