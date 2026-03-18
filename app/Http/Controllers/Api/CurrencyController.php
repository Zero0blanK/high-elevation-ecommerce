<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    public function index(): JsonResponse
    {
        $currencies = $this->currencyService->getActiveCurrencies();
        return response()->json(['currencies' => $currencies]);
    }

    public function convert(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'to' => 'required|string|size:3',
            'from' => 'nullable|string|size:3',
        ]);

        $converted = $this->currencyService->convert(
            $request->amount,
            $request->to,
            $request->from
        );

        return response()->json([
            'original' => $request->amount,
            'converted' => $converted,
            'to' => $request->to,
            'from' => $request->from ?? 'USD',
        ]);
    }
}
