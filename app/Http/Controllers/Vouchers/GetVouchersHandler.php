<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Response;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): Response
    {
        $vouchers = $this->voucherService->getVouchers(
            $request->query('page'),
            $request->query('paginate'),
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('number'),
            $request->query('series'),
        );

        return response([
            'data' => VoucherResource::collection($vouchers),
        ], 200);
    }

    public function getVouchersTotalAmount()
    {
        try {
            // Obtener el usuario actual
            $user = auth()->user();
            // Agrupar y sumar los vouchers por el currency
            $vouchers_total_amount = $user->vouchers()->groupBy('currency')
                ->selectRaw('currency, SUM(total_amount) as total_amount')
                ->get();

            return response([
                'data' => $vouchers_total_amount,
            ], 200);
        } catch (Exception $exception) {

            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
