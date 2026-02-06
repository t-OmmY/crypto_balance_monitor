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
final readonly class BlockchairProvider implements BalanceProviderInterface
{
    private const int SATOSHI_DELIMITER = 100000000;

    private const array SUPPORTED_CURRENCIES_NETWORKS = [
        'BTC' => 'bitcoin',
        'LTC' => 'litecoin',
    ];

    #[Override]
    public function support(string $currency): bool
    {
        return true === isset(self::SUPPORTED_CURRENCIES_NETWORKS[$currency]);
    }

    #[Override]
    public function getBalance(string $currency, string $address): BigDecimal
    {
        //todo retries??? try_catch???
        /** @var string $network */
        $network = self::SUPPORTED_CURRENCIES_NETWORKS[$currency];
        $response = Http::get(
            "https://api.blockchair.com/{$network}/dashboards/address/{$address}"
        );

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
}
