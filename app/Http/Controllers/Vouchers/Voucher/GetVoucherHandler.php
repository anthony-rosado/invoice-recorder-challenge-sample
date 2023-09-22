<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Models\Voucher;
use Illuminate\Http\Request;


class GetVoucherHandler
{
    public function index(Request $request)
{
    $query = Voucher::query();

    // Filtrar por serie
    if ($request->has('serie')) {
        $query->where('serie', $request->input('serie'));
    }

    // Filtrar por número
    if ($request->has('numero')) {
        $query->where('numero', $request->input('numero'));
    }

    // Filtrar por rango de fechas
    if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
        $query->whereBetween('created_at', [$request->input('fecha_inicio'), $request->input('fecha_fin')]);
    }

    // Obtener los resultados
    $vouchers = $query->get();

    return response()->json(['data' => $vouchers], 200);
}

}
