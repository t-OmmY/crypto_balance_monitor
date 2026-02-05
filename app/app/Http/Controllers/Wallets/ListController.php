<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class ListController extends Controller
{
    public function __invoke(PaginationRequest $request): JsonResponse
    {
        $wallets = Wallet::paginate($request->getPerPage());

        return response()->json($wallets);
    }
}
