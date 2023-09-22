<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Models\Voucher;
use Illuminate\Http\Response;

class DeleteVoucherHandler
{
    public function __invoke($id)
    {
        // Buscar el comprobante por su ID
        $voucher = Voucher::find($id);

        // Verificar si el comprobante existe
        if (!$voucher) {
            return response(['message' => 'Comprobante no encontrado.'], 404);
        }

        // Eliminar el comprobante
        $voucher->delete();

        return response(['message' => 'Comprobante eliminado correctamente.'], 200);
    }
}
