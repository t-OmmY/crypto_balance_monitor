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

final readonly class EtherscanProvider implements BalanceProviderInterface
{
    private const int WEI_ETH_DELIMITER = 100000000000000000;

    private const array SUPPORTED_CURRENCIES_CHAIN_IDS = [
        Currency::ETH->value => 1,
    ];

    public function __construct(
        private null|string $apiKey,
        private string $baseUrl
    ) {
    }

    #[Override]
    public function support(Currency $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency->value]);
    }

    /**
     * @throws ConnectionException
     */
    #[Override]
    public function getBalance(Currency $currency, string $address): BigDecimal
    {
        if (null === $this->apiKey || '' === $this->apiKey) {
            throw new BalanceProviderException('Etherscan API key is not configured');
        }

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
    private function request(Currency $currency, string $address): Response|PromiseInterface
    {
        /** @var int $chainId */
        $chainId = self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency->value];

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
