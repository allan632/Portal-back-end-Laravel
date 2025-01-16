<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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

         // Validação

        
        

        
        // Verificar as credenciais
        if (Auth::attempt(['email' => $request->input("DsEmail"), 'password' => $request->input("DsSenha")])) {

            // Gerar token
            $token = $request->user()->createToken('NomeDoSeuApp')->plainTextToken;

            // Retornar token como resposta
            return response()->json([
                'token' => $token,
            ]);
        }

        $credentials = $request->only('DsEmail', 'DsSenha');

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('Token Name')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }
    
        return response()->json(['error' => 'Credenciais inválidas'], 401);
    }

}