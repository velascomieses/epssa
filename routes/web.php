<?php

use App\Http\Controllers\Plataforma\PagoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Plataforma\ContratoController;
use App\Http\Controllers\Plataforma\ProductoController;

Route::get('/', function () {
    // redirect to admin dashboard /admin
    return redirect('/admin');
});
Route::prefix('contratos')->middleware(['auth'])->group(function () {
    Route::get('/{id}/cronograma', [ContratoController::class, 'verCronograma'])->name('contratos.rpt.cronograma');
    Route::get('/{id}/historial-pagos', [ContratoController::class, 'verHistorialPago'])->name('contratos.rpt.historial.pago');
    Route::get('/{id}/historial-otros-pagos', [ContratoController::class, 'verOtroPago'])->name('contratos.rpt.historial.otros.pagos');
    Route::get('/{id}/contrato', [ContratoController::class, 'verContrato'])->name('contratos.contrato');
});

Route::prefix('productos')->middleware(['auth'])->group(function () {
    Route::get('/{id}/bc', [ProductoController::class, 'verBarCode'])->name('producto.bc');
});

Route::prefix('pagos')->middleware(['auth'])->group(function () {
    Route::get('/{id}/voucher', [PagoController::class, 'voucher'])->name('pagos.voucher');
});
