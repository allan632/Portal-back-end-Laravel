<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    // Função para encriptar a senha usando OpenSSL
    // "\0" adiciona um byte nulo para que o total de bytes seja = 16 e a encryptação funcione!
    protected function encryptPassword($password)
    {
        $ciphering = "AES-128-CTR";
        $options = 0;
        $encryption_iv = '123456789101112' . "\0"; 
        $encryption_key = "8gPoW^&Io6^C*qg^";

        $encryptedPassword = openssl_encrypt($password, $ciphering, $encryption_key, $options, $encryption_iv);
        
        return $encryptedPassword;
    }

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

    // Função de registro para salvar o usuário com a senha criptografada
    public function register(Request $request)
    {
        $senhaOriginal = $request->input('DsSenha');
        $encryptedPassword = $this->encryptPassword($senhaOriginal);

        $user = User::create([
            'NrFun' => $request->input('NrFun'),
            'DsLogin' => $request->input('DsLogin'),
            'DsSenha' => $request->input('DsSenha'),
        ]);

        return response(['message' => 'Usuário registrado com sucesso'], Response::HTTP_CREATED);
    }

    // Função de login para verificar o usuário e descriptografar a senha
    // Busca o usuário para validar se existe, caso não exista ele irá parar a função e retornar uma mensagem de "Usuário não encontrado"
    // Caso encontre o usuário irá validar se a senha colocada pelo usuário é igual a senha buscada pelo banco e desencriptada
    // Caso a senha esteja incorreta irá retornar uma mensagem de "Senha incorreta", do contrário ele irá retornar o bem vindo.
    public function login(Request $request)
    {
        $login = $request->input('DsLogin');
        $senha = $request->input('DsSenha');

        $user = User::where('DsLogin', $login)->first();

        if (!$user) {
            return response([
                "message" => "Usuário não encontrado"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $decryptedPassword = $this->decryptPassword($user->DsSenha);

        if ($decryptedPassword === $senha) {
            Auth::login($user);
            return response(['message' => 'Login bem-sucedido'], Response::HTTP_OK);
        } else {
            return response(['message' => 'Senha incorreta'], Response::HTTP_UNAUTHORIZED);
        }
    }

    // Função responsável por pegar todos os usário cadastrados do banco de dados, sem válidar se é ativou o inativo
    public function obterUsuarios()
    {
        $users = User::all();

        return $users;
    }
}
