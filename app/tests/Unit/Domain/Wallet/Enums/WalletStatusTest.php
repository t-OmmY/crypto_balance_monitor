<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Wallet\Enums;

use App\Domain\Wallet\Enums\WalletStatus;
use PHPUnit\Framework\TestCase;

final class WalletStatusTest extends TestCase
{
    public function test_created_is_updatable(): void
    {
        self::assertTrue(WalletStatus::CREATED->isUpdatable());
    }

    public function test_active_is_updatable(): void
    {
        self::assertTrue(WalletStatus::ACTIVE->isUpdatable());
    }

    public function test_syncing_is_not_updatable(): void
    {
        self::assertFalse(WalletStatus::SYNCING->isUpdatable());
    }

    public function test_failed_is_not_updatable(): void
    {
        self::assertFalse(WalletStatus::FAILED->isUpdatable());
    }
}
