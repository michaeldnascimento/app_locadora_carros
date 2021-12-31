<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciais = $request->all('email', 'password'); //[]

        //autenticaćão (email e senha)
        $token = auth('api')->attempt($credenciais);

        if($token) {//usuario autenticado com sucesso\

            return response()->json(['token' => $token]);

        } else { //erro de usuário ou senha

            return response()->json(['erro' => 'Usuário ou senha inválido'], 403);
            //403 - proibido
        }

        //retornar um json Web Token
        return 'login';
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout realizado com sucesso']);
    }

    public function refresh()
    {
        $token = auth('api')->refresh(); //cliente encaminhe um jwt valido
        return response()->json(['token' => $token]);

}

    public function me()
    {
        return response()->json(auth()->user());
    }
}
