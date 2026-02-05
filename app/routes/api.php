<?php declare(strict_types=1);

use App\Http\Controllers\Wallets\CreateController;
use App\Http\Controllers\Wallets\GetController;
use App\Http\Controllers\Wallets\ListController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallets')->group(function () {
    Route::post('/', CreateController::class);
    Route::get('/', ListController::class);
    Route::get('/{id}', GetController::class);
});
