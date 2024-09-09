<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function registro(Request $request)
    {   
        $user = User::create([
            'NrFun' => $request->input('NrFun'),
            'DsLogin' => $request->input('DsLogin'),
            'DsSenha' => $request->input('DsSenha')
        ]);

        return $user;
    }

    public function obterUsuarios()
    {
        $users = User::all();

        return $users;
    }


    public function user()
    {
        return "hello :D";
    }
}
