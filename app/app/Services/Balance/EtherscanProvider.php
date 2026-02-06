<?php

declare(strict_types=1);

namespace App\Services\Balance;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\Http;
use Override;

/**
 * @psalm-api
 */
final readonly class EtherscanProvider implements BalanceProviderInterface
{
    private const int WEI_ETH_DELIMITER = 100000000000000000;

    private const array SUPPORTED_CURRENCIES_CHAIN_IDS = [
        'ETH' => 1,
    ];

    #[Override]
    public function support(string $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency]);
    }

    #[Override]
    public function getBalance(string $currency, string $address): BigDecimal
    {
        //todo try catch retries?

        /** @var int $chainId */
        $chainId = self::SUPPORTED_CURRENCIES_CHAIN_IDS[$currency];
        $apikey = config('services.etherscan.key');
        $response = Http::get('https://api.etherscan.io/v2/api', [
            'chainid' => $chainId,
            'module' => 'account',
            'action' => 'balance',
            'address' => $address,
            'tag' => 'latest',
            'apikey' => $apikey,
        ]);

        if (false === $response->successful()) {
            throw new BalanceProviderException('Etherscan API error');
        }

        $result = $response->json('result');
        if (false === is_numeric($result)) {
            throw new BalanceProviderException('Invalid Etherscan response');
        }

        return BigDecimal::of($result)->dividedBy(self::WEI_ETH_DELIMITER, 18, RoundingMode::Down);
    }
}
