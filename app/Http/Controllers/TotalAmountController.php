<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;

class TotalAmountController extends Controller
{
    public function getTotalAmount(Request $request)
    {
        // Realiza el cálculo de los montos totales en soles y dólares
        $totalAmountSoles = Voucher::where('moneda', 'PEN')->sum('total_amount');
        $totalAmountDollars = Voucher::where('moneda', 'USD')->sum('total_amount');

        return response()->json([
            'total_amount_soles' => $totalAmountSoles,
            'total_amount_dollars' => $totalAmountDollars,
        ]);
    }
}
