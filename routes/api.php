<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SacController;

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

Route::middleware(Authenticate::class)->group(function () {
    Route::get('/', [AuthController::class, view('welcome')]);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/obterUsuarios', [AuthController::class, 'obterUsuarios']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);

// Route::post('/filter-occurrence', [SacController::class, 'filterOccurrence']);
// Route::post('/register-evolution', [SacController::class, 'createEvolution']);
// Route::post('/register-occurrence', [SacController::class, 'createOccurrence']);