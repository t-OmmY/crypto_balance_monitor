<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Enums\Currency;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Override;

final readonly class BlockchairProvider implements BalanceProviderInterface
{
    private const int SATOSHI_DELIMITER = 100000000;

    private const array SUPPORTED_CURRENCIES_NETWORKS = [
        Currency::BTC->value => 'bitcoin',
        Currency::LTC->value => 'litecoin',
    ];

    public function __construct(
        private null|string $apiKey,
        private string $baseUrl
    ) {
    }

    #[Override]
    public function support(Currency $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_NETWORKS[$currency->value]);
    }

    /**
     * @throws ConnectionException
     */
    #[Override]
    public function getBalance(Currency $currency, string $address): BigDecimal
    {
        if (null === $this->apiKey || '' === $this->apiKey) {
            throw new BalanceProviderException('Blockchair API key is not configured');
        }

        $response = $this->request($currency, $address);

        if (false === $response->successful()) {
            throw new BalanceProviderException('Blockchair API error');
        }

        $balance = data_get($response->json(), "data.{$address}.address.balance");
        if (false === is_numeric($balance)) {
            throw new BalanceProviderException('Invalid Blockchair response');
        }

        return BigDecimal::of($balance)
            ->dividedBy(self::SATOSHI_DELIMITER, 18, RoundingMode::Down);
    }

    /**
     * @throws ConnectionException
     */
    private function request(Currency $currency, string $address): Response|PromiseInterface
    {
        /** @var string $network */
        $network = self::SUPPORTED_CURRENCIES_NETWORKS[$currency->value];

        return Http::retry(
            times: 3,
            sleepMilliseconds: 500,
            throw: false
        )->withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get(
            "{$this->baseUrl}/{$network}/dashboards/address/{$address}"
        );
    }
}
