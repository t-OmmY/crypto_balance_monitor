<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWalletRequest;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    public function __invoke(CreateWalletRequest $request): JsonResponse
    {
        return response()->json(['data' => $request->all()]);
    }
}
