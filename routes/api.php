<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/quotes', [QuoteController::class, 'store']);

Route::get('/quotes', [QuoteController::class, 'index']);

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});
