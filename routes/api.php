<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PalletController;
use App\Http\Controllers\PortalController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::put('/', function (Request $request) {
    return response()->json(['message' => "hello word"]);
});

Route::post('/register',[AuthController::class,"registerAuth"]);

Route::post('/login', [AuthController::class,"loginAuth"]);

Route::middleware('auth:api','profile:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, "viewProfile"]);

    Route::post("/logout",[AuthController::class,"logoutAuth"]);
    Route::post('/register/profile',[ProfileController::class, "createProfile"]);
    Route::put('/link/profile/user',[ProfileController::class, "linkProfileToUser"]);

});
Route::post('/consult/entry/palletData',[PalletController::class, "consultRemetentEntryPallet"]);

Route::middleware('auth:api','profile:PalletController')->group(function () {
    Route::post('/register/entry/palletData',[PalletController::class, "registerEntryPallet"]);
    Route::post('/register/entry/palletPhoto',[PalletController::class, "registerPhotoDocAuxiliarEntry"]);
    Route::post('/test',[PalletController::class, "test"]);
    Route::get('/Portal/SearchFilias',[PortalController::class, "searchFilias"]);
    Route::get('/Portal/SearchDestinatariosRemetentes',[PortalController::class, "searchDestinatariosRemetentes"]);
    Route::post('/register/Exit/palletData',[PalletController::class, "registerExitPallet"]);
    Route::post('/register/Exit/palletData',[PalletController::class, "registerExitPallet"]);
    Route::post('/consult/Exit/PalletFilial', [PalletController::class, "CheckStorePalletFilial"]);
    Route::post('/register/exit/palletPhotoFisc',[PalletController::class, "registerPhotoPaleteAuxiliarEntry"]);

});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/auth/verify', [AuthController::class,"verifyToken"]);
});
