<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Função para encriptar a senha usando OpenSSL
    // "\0" adiciona um byte nulo para que o total de bytes seja = 16 e a encryptação funcione!

    // Função para descriptografar a senha usando OpenSSL
    // "\0" adiciona um byte nulo para que o total de bytes seja = 16 e a desencriptação funcione
    protected function decryptPassword($encryptedPassword)
    {
        $ciphering = "AES-128-CTR";
        $options = 0;
        $decryption_iv = '123456789101112' . "\0"; 
        $decryption_key = "8gPoW^&Io6^C*qg^";

        $decryptedPassword = openssl_decrypt($encryptedPassword, $ciphering, $decryption_key, $options, $decryption_iv);
        
        return $decryptedPassword;
    }

    public function loginAuth(Request $request){
        $loginName = $request->input('DsLogin');
        $loginPassword = $request->input('DsSenha');
         // Validação
        $user = User::where('name', $loginName)->first();
         
        if (!$user) return response(["message" => "Usuário não encontrado"], ); #Response::HTTP_UNAUTHORIZED

        
        if(Hash::check($loginPassword, $user->password)) 
        {
            $token = $user->createToken('remember_token');
            $encryptedToken = Crypt::encryptString($token);

            return response()->json(['token' => $token], 200);
        }
        
         
    
        return response()->json(['error' => " Credenciais inválidas"], 401);
    }

}