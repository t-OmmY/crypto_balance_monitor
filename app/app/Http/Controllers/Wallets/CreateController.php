<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWalletRequest;
use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(CreateWalletRequest $request): JsonResponse
    {
        //todo move to other layer
        DB::beginTransaction();
        try {
            $wallet = Wallet::create([
                'address' => $request->get('address'),
                'currency' => $request->get('currency'),
                'last_balance' => 0,
            ]);

            WalletBalanceHistory::create([
                'wallet_id' => $wallet->getAttribute('id'),
                'balance' => 0,
                'created_at' => now(),
            ]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return response()->json($wallet, 201);
    }
}
