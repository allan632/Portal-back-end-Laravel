<?php

use App\Models\User;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessTokenResult;

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


Route::get('/', function (Request $request) {
    return response()->json(['message' => "hello word"]);
});

Route::post('/register',[AuthController::class,"registerAuth"]);

Route::post('/login', [AuthController::class,"loginAuth"]);


Route::middleware('auth:api')->group(function () {
    Route::get('/profile', function () {
        return response()->json(Auth::user());
    });
    Route::post("/logout",[AuthController::class,"logoutAuth"]);

});
