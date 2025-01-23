<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller 
{
    public function registerAuth(Request $req)
    {
        // set the default timezone to use.
    date_default_timezone_set('UTC');


    // Cria o novo usuário
    $user = User::create([
        'IdFunWMS'=>'2',
        'CdSideBar'=>0,
        'DsLogin' => $req->DsLogin,
        'password'=>$req->DsSenha,
        'updated_at'=>ltrim(now()->format('y-m-d H:i:s'), "\n"),
        'created_at'=>ltrim(now()->format('y-m-d H:i:s'), "\n")
        ,

    ]);

    
    
    return response()->json([
        'message' => 'Usuário registrado com sucesso!',
        'token' => $user,
    ], 201);
    }

    public function loginAuth(Request $req) 
    {
    
    $user = User::where('DsLogin', $req->DsLogin)->first();
    
    if (!Auth::attempt(['DsLogin' => $req->DsLogin, 'password' => $req->DsSenha])) return response()->json(["erro"], 401);
    

     // Verifique se esse valor não é nulo
     $token = JWTAuth::fromUser($user);


    return response()->json(['token' => $token, 201]);
    }
}