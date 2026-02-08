<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Domain\Wallet\Commands\CreateWallet;
use App\Domain\Wallet\Commands\CreateWalletHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallets\CreateRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateController extends Controller
{
    public function __construct(
        private readonly CreateWalletHandler $createWalletHandler
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(CreateRequest $request): JsonResponse
    {
        $command = new CreateWallet($request->getAddress(), $request->getCurrency());
        $result = $this->createWalletHandler->handle($command);

        return response()->json(
            ['id' => $result->getWalletId()],
            $result->isAlreadyExists() ? Response::HTTP_OK : Response::HTTP_CREATED
        );
    }
}
