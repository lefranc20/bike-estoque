<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\MovimentacaoEstoqueController;
use App\Http\Controllers\Api\DashboardController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('produtos', ProdutoController::class);

    Route::get('movimentacoes', [MovimentacaoEstoqueController::class, 'index']);
    Route::post('movimentacoes', [MovimentacaoEstoqueController::class, 'store']);

    Route::get('dashboard', [DashboardController::class, 'index']);
});