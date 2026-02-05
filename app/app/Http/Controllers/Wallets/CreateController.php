<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use app\Http\Requests\Wallets\CreateRequest;
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
    public function __invoke(CreateRequest $request): JsonResponse
    {
        //todo move to other layer
        $wallet = Wallet::where(['address' => $request->getAddress(), 'currency' => $request->getCurrency()])->first();
        if (null !== $wallet) {
            return response()->json($wallet);
        }

        DB::beginTransaction();
        try {
            $wallet = Wallet::create([
                'address' => $request->getAddress(),
                'currency' => $request->getCurrency(),
                'last_balance' => 0,
            ]);

            WalletBalanceHistory::create([
                'wallet_id' => $wallet->getId(),
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
