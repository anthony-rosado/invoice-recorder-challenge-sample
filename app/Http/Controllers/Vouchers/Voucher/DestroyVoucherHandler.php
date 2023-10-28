<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class DestroyVoucherHandler extends Controller
{
    public function __invoke($id)
    {
        try {
            // Obtener el usuario actual
            $user = auth()->user();
            // Buscar el voucher 
            $voucher = $user->vouchers()->where('id', $id)->first();
            /* $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first(); */

            // Comprobar si el voucher existe
            if (!$voucher) {
                return response(['message' => 'Comprobante no encontrado'], 404);
            }

            // En caso exista lo borramos a nivel softdelete
            $voucher->delete();

            return response([
                'message' => 'El comprobante se ha eliminado correctamente',
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
