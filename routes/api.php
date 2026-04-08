<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AdminAuthController;

// 這行會自動產生 GET, POST, PUT, DELETE 等對應路徑
Route::apiResource('tests', TestController::class);

//測試 api token
Route::get('/apiLogin', [AdminAuthController::class, 'apiLogin']);  //登入取得 token
Route::post('/userInfo', function (Request $request) {
    return $request->user();  //只要有帶正確 Token，這裡就能抓到當前使用者
})->middleware('auth:sanctum');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 測試發送 gemini API
// http://127.0.0.1:8080/api/geminiapi
Route::get('/geminiapi', [TestController::class, 'geminiapi']);

