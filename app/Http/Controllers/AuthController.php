<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Profile;

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
        try {

            $user = User::where('NrFun', $req->NrFun)->first();
            if($user) 
                return response()->json(['message' => 'Usuario ja cadastrado',], 422);
        
            // Cria o novo usuário
            $user = User::create([
                'NrFun'=>trim($req->NrFun),
                'IdFunWMS'=>trim($req->IdFunWMS),
                'CdSideBar'=>trim($req->CdSideBar),
                'DsLogin' => trim($req->DsLogin),
                'password'=> trim($req->DsSenha),
                'access_level'=>trim($req->access_level),
                'profile_name'=>trim($req->profile_name)


            ]);

            $profile = Profile::where('name', $req->profile_name)->first();

            // associando papéis existentes
            $user->profiles()->attach([ $profile->id]); // Associando IDs dos papéis já existentes
                    return response()->json([
                        'message' => 'Usuário registrado com sucesso!',
                        'user' => $user,
                    ], 201);


        } catch(\Exception $e) {
            return response()->json(['message' => "erro" ], 403);
                
        }
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
        $profile_name = $user->profiles->map(function ($profile) {
            return $profile->name;
        });

        $access_level = $user->profiles->map(function ($profile) {
            return $profile->access_level; // Assumindo que o nome do perfil está na coluna `name`
            
        });

        

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day
        $user->profiles();
        return response()->json([
            'user' => (Object) array(
                "id"=>$user->id,
                "name"=>$req->DsLogin,
                'permissions' =>  $profile_name[0],
                'prioritPermission' =>$access_level[0]
            ),
            'token' => $token,
        ],201)->cookie(($cookie), Response::HTTP_ACCEPTED);
    }

    public function verifyToken(Request $req){
        
        return response()->json(["user"=>"sucess"],201);
    }

    public function logoutAuth()
    {
        Cookie::forget('token');
        Session::forget('PHPSESSID');
        return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
    }
}