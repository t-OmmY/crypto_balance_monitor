<?php

return [
    'use_fake' => env('BALANCE_FAKE', true),

    'providers' => [
        \App\Services\Balance\BlockchairProvider::class,
        \App\Services\Balance\EtherscanProvider::class,
    ],
];
