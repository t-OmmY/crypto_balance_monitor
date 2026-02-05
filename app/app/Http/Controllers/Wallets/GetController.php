<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Domain\Wallet\Queries\GetWalletById;
use App\Domain\Wallet\Queries\GetWalletByIdHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallets\GetRequest;
use Illuminate\Http\JsonResponse;

final class GetController extends Controller
{
    public function __construct(
        private readonly GetWalletByIdHandler $getWalletByIdHandler
    ) {
    }

    public function __invoke(GetRequest $request): JsonResponse
    {
        $query = new GetWalletById($request->getId());
        $wallet = $this->getWalletByIdHandler->handle($query);

        return response()->json($wallet);
    }
}
