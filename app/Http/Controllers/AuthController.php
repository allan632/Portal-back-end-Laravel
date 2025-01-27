<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller 
{
    public function registerAuth(Request $req)
    {
        $user = User::where('NrFun', $req->NrFun)->first();
        if($user) 
            return response()->json(['message' => 'Usuario ja cadastrado',], 422);
        // Cria o novo usuário
        $user = User::create([
            'NrFun'=>trim($req->NrFun),
            'IdFunWMS'=>'2',
            'CdSideBar'=>0,
            'DsLogin' => trim($req->DsLogin),
            'password'=> trim($req->DsSenha),
            'DsAbastTalaoEx' => $req->DsAbastTalaoEx,
            'DsAbastTalao'=> $req->DsAbastTalao

        ]);
        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'token' => $user,
        ], 201);
    }

    public function loginAuth(Request $req) 
    {
        $user = User::where('DsLogin', $req->DsLogin)->first();
        if(!$user ) return response()->json(["erro"=>"usuario não existe"], 401);
        if (!Auth::attempt(['DsLogin' => $req->DsLogin, 'password' => $req->DsSenha])) 
            return response()->json(["erro"=>"senha ou nome invalido"], 401);
        // Verifique se esse valor não é nulo
        $token = JWTAuth::fromUser($user);

        Auth::login($user);
        Session::put('PHPSESSID', $token);

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day

        return response()->json([
            'message' => 'Success',
            'token' => $token,
            'user' => $req->DsLogin,
            'nrFun' => $user->NrFun,
            'permissions' =>  $req->DsLogin
        ],201)->cookie(($cookie), Response::HTTP_ACCEPTED);
    }

    public function logoutAuth()
    {
        Cookie::forget('token');
        Session::forget('PHPSESSID');
        return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
    }
}