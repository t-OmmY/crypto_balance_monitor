<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Domain\Wallet\Queries\ListWallets;
use App\Domain\Wallet\Queries\ListWalletsHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
use Illuminate\Http\JsonResponse;

final class ListController extends Controller
{
    public function __construct(
        private readonly ListWalletsHandler $listWalletsHandler
    ) {
    }

    public function __invoke(PaginationRequest $request): JsonResponse
    {
        $query = new ListWallets($request->getPerPage());
        $list = $this->listWalletsHandler->handle($query);

        return response()->json($list);
    }
}
