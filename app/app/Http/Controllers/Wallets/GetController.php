<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallets\GetRequest;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class GetController extends Controller
{
    public function __invoke(GetRequest $request): JsonResponse
    {
        //todo move to better place
        $wallet = Wallet::find($request->getId());

        return response()->json($wallet);
    }
}
