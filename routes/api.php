<?php

use App\Models\User;
use App\Models\Personal_access_tokens;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
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


    return response()->json(['token' => "hello word"]);
});
Route::post('/login', function (Request $request) {
    $credentials = $request->only("DsLogin","DsSenha");
    
    
    $user = User::where('DsLogin', $request->DsLogin)->first();
    
    if (!Auth::attempt(['DsLogin' => $request->DsLogin, 'password' => $request->DsSenha])) return response()->json(["erro"], 401);
    

     // Verifique se esse valor não é nulo
     $token = JWTAuth::fromUser($user);


    return response()->json(['token' => $token, 201]);
});
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', function () {
        return response()->json(Auth::user());
    });
});
Route::post('/register', function (Request $request) {

    // Cria o novo usuário
    $user = User::create([
        'IdFunWMS'=>'2',
        'CdSideBar'=>0,
        'DsLogin' => $request->DsLogin,
        'password'=>$request->DsSenha

    ]);

    
    
    return response()->json([
        'message' => 'Usuário registrado com sucesso!',
        'token' => $user,
    ], 201);
});
