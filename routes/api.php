<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;

use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/create/user", [UsersController::class, "register"]);

Route::post('/tokens/create',[AuthController::class, "loginAuth"]);

Route::middleware('auth:sanctum')->get('/protected-route', function (Request $request) {
    return request()->json(["messagem voce esta logado" => "Credencia invalidas"], 201);
});        
       
Route::get('/hellowWord', [AuthController::class, "loginAuth"] );