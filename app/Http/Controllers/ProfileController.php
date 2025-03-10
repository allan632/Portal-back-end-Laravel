<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
{
    //
    public function viewProfile(Request $req){
        return response()->json(['message' => DB::select('select * from profiles'),], 201);
    }

    //funcao sera responsavel por vincular o perfil no usuario
    public function linkProfileToUser(Request $req){
        try{
            //validacao se existe
            $data = $req->validate([
                'user_name' => 'required|string|max:255',
                'profile_name' => 'required',
            ]);
            $user = User::where('DsLogin', $req->user_name)->first();

            $profile = Profile::where('name', $req->profile_name)->first();

            if(!$user)return response()->json(["erro"=>"usuario não existe"], 401);
            if(!$profile)return response()->json(["erro"=>"perfil não existe"], 401);
            
            // associando papéis existentes
            $user->profiles()->attach([ $profile->id]); // Associando IDs dos papéis já existentes
            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'user' => $user,
            ], 201);

        } catch( \Exception $e){

            return response()->json(['message' => $e ], 403);
        }

        
    }

    public function createProfile(Request $req){
        try {
            
     
        $verifyProfile = Profile::where('name', 'admin')->first();

        if($verifyProfile->access_level === $req->accessLevel) 
            return response()->json(['message' => 'acesso/perfil ja cadastrado',], 422);
        if(!(intval($req->accessLevel) < 5 && intval($req->accessLevel) > 0))
            return response()->json(['message' => 'perfil indiferente de 1, 2, 3 ou 4',], 422);
        

        $profile = Profile::create([
            'name'=>$req->nameProfile,
            'access_level'=>$req->accessLevel
        ]);
        
        return response()->json(['message' => "perfil criado $profile",], 201);
    } catch(\Exception $e){
        return response()->json(['erro' => "$e",], 422);
    }
    }
}
