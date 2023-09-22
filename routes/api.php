<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TotalAmountController; 
use App\Http\Controllers\Vouchers\Voucher\DeleteVoucherHandler;

include_once 'v1/no-auth.php';

//requerimiento nro3
Route::get('/total-amount', [TotalAmountController::class, 'getTotalAmount']);

//requerimiento nro4
Route::delete('/vouchers/{id}', [DeleteVoucherHandler::class, '__invoke']);

//requerimiento nro5
Route::get('/vouchers', 'Vouchers\Voucher\GetVouchersHandler@index')->name('vouchers.index');


Route::group(['middleware' => ['jwt.verify']], function () {
    include_once 'v1/auth.php';
});