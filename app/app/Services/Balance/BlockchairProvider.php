<?php

declare(strict_types=1);

namespace App\Services\Balance;

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
        'BTC' => 'bitcoin',
        'LTC' => 'litecoin',
    ];

    public function __construct(
        private null|string $apiKey,
        private string $baseUrl
    ) {
    }

    #[Override]
    public function support(string $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_NETWORKS[$currency]);
    }

    /**
     * @throws ConnectionException
     */
    #[Override]
    public function getBalance(string $currency, string $address): BigDecimal
    {
        if (null === $this->apiKey || '' === $this->apiKey) {
            throw new BalanceProviderException('Blockchair API key is not configured');
        }

        $response = $this->request($currency, $address);

        if (false === $response->successful()) {
            throw new BalanceProviderException('Blockchair API error');
        }

        $data = $response->json('data');
        $balance = $data[$address]['address']['balance'] ?? null;

        if (false === is_numeric($balance)) {
            throw new BalanceProviderException('Invalid Blockchair response');
        }

        return BigDecimal::of($balance)->dividedBy(self::SATOSHI_DELIMITER, 18, RoundingMode::Down);
    }

    /**
     * @throws ConnectionException
     */
    private function request(string $currency, string $address): Response|PromiseInterface
    {
        /** @var string $network */
        $network = self::SUPPORTED_CURRENCIES_NETWORKS[$currency];

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
