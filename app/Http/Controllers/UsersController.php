<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\DB;
 
class UsersController extends Controller {
        // Função de registro para salvar o usuário com a senha criptografada
        public function register(Request $request)
        {
            $passwordOriginal = $request->input('DsSenha');
            $nameOriginal = $request->input("DsLogin");
            $emailOriginal = $request->input("DsEmail");
            
            if(!$passwordOriginal) return response()->json(['erro' => 'password esta vazio'], 422);
            if(!$nameOriginal) return response()->json(['erro' => 'Nome esta vazio'], 422);
            if(!$emailOriginal) return response()->json(['message' => 'email esta vazio'], 422);
            try {
                DB::select("INSERT INTO users (name, email, password, created_at, updated_at)
                VALUES ('$nameOriginal', '$emailOriginal', '$passwordOriginal', GETDATE(), GETDATE());");
                return response()->json(['message' => 'cadastrado com sucesso'], 201);

            } catch (Exception $e) {
                return response()->json(['message' => 'Credenciais inválidas'], 422);
            } 

        }
}