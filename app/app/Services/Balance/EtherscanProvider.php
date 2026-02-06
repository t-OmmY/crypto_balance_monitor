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

final readonly class EtherscanProvider implements BalanceProviderInterface
{
    private const int WEI_ETH_DELIMITER = 100000000000000000;

    private const array SUPPORTED_CURRENCIES_CHAIN_IDS = [
        'ETH' => 1,
    ];

    public function __construct(
        private null|string $apiKey,
        private string $baseUrl
    ) {
    }

    #[Override]
    public function support(string $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency]);
    }

    /**
     * @throws ConnectionException
     */
    #[Override]
    public function getBalance(string $currency, string $address): BigDecimal
    {
        if (null === $this->apiKey || '' === $this->apiKey) {
            throw new BalanceProviderException('Etherscan API key is not configured');
        }

        //todo try catch retries?

        $response = $this->request($currency, $address);
        if (false === $response->successful()) {
            throw new BalanceProviderException('Etherscan API error');
        }

        $result = $response->json('result');
        if (false === is_numeric($result)) {
            throw new BalanceProviderException('Invalid Etherscan response');
        }

        return BigDecimal::of($result)->dividedBy(self::WEI_ETH_DELIMITER, 18, RoundingMode::Down);
    }

    /**
     * @throws ConnectionException
     */
    private function request(string $currency, string $address): Response|PromiseInterface
    {
        /** @var int $chainId */
        $chainId = self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency];

        return Http::retry(
            times: 3,
            sleepMilliseconds: 500,
            throw: false
        )->get($this->baseUrl, [
            'chainid' => $chainId,
            'module' => 'account',
            'action' => 'balance',
            'address' => $address,
            'tag' => 'latest',
            'apikey' => $this->apiKey,
        ]);
    }
}
