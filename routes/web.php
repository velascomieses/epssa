<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Plataforma\ContratoController;
use App\Http\Controllers\Plataforma\ProductoController;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('contratos')->middleware(['auth'])->group(function () {
    Route::get('/{id}/cronograma', [ContratoController::class, 'verCronograma'])->name('contratos.rpt.cronograma');
    Route::get('/{id}/historial-pagos', [ContratoController::class, 'verHistorialPago'])->name('contratos.rpt.historial.pago');
});

Route::prefix('productos')->middleware(['auth'])->group(function () {
    Route::get('/{id}/bc', [ProductoController::class, 'verBarCode'])->name('producto.bc');
});
