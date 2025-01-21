<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {
        // Função de registro para salvar o usuário com a senha criptografada
        public function register(Request $request)
        {
            //Criação de variavel
            $passwordOriginal = $request->input('DsSenha');
            $nameOriginal = $request->input("DsLogin");
            $emailOriginal = $request->input("DsEmail");
            //Validação de entrada
            if(!$passwordOriginal) return response()->json(['erro' => 'password esta vazio'], 422);
            if(!$nameOriginal) return response()->json(['erro' => 'Nome esta vazio'], 422);
            if(!$emailOriginal) return response()->json(['message' => 'email esta vazio'], 422);
            try {
                ""
                //Seguraça e auth
                //Query sql
                $sql =  "INSERT INTO users (NrFun, DsLogin, password, created_at, updated_at)
                VALUES ((SELECT IFNULL(MAX(id), 0) + 1 FROM users) ,? ,? , GETDATE(), GETDATE())";


                //requizição para p banco
                DB::statement($sql, [$nameOriginal, $emailOriginal, $passwordOriginal]);

                return response()->json(['message' => 'Cadastro realizado'], 201);
            } catch (Exception $e) {
                return response()->json(['message' => 'Credenciais inválidas'], 422);
            } 
        }
}

