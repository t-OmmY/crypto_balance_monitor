<?php declare(strict_types=1);

namespace App\Http\Controllers\Wallets;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetController extends Controller
{
    public function __invoke(string $id): JsonResponse
    {
        return response()->json(['action' => 'get', 'id' => $id]);
    }
}
