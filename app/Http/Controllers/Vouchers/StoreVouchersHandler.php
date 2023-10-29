<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\StoreVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Jobs\ProcessStoreVouchersFromXmlContents;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(StoreVouchersRequest $request): Response
    {

        try {
            $files = $request->file('files');

            if (!is_array($files)) {
                $files = [$files];
            }

            $xmlFiles = [];
            foreach ($files as $file) {
                $xmlFile = [
                    'xmlContents' => file_get_contents($file->getRealPath()),
                    'fileName' => $file->getClientOriginalName(),
                ];

                $xmlFiles[] = $xmlFile;
            }

            $user = auth()->user();

            ProcessStoreVouchersFromXmlContents::dispatch($xmlFiles, $user);

            return response([
                'message' => 'Hemos recibido tus comprobantes y estamos procesándolos. Pronto recibirás un correo de confirmación con los resultados.',
            ], 201);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
