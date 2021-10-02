<?php

namespace App\Http\Controllers;
/* namespace App\Http\Controllers; */

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //autenticação (email e senha)
        //Método "all()" retorna um array.
        $credenciais = $request->all(['email', 'password']);

        //retornar um Json Web Token:
        //Para autenticar: Método/helper "auth()" do laravel
        //Método "attempt()": Faz uma tentativa de autenticação
        //e se der certo, ele retorna um "token"(api).
        $token = auth('api')->attempt($credenciais);
        /* dd($token); */
        if ($token) { //Usuário autenticado com sucesso,
            return response()->json(['token' => $token]);
        } else { //Êrro de usuário ou senha
            return response()->json(['erro' => 'Usuário ou senha incorretos!'], 403);
            //401 = Unauthorized -> não autorizado
            //403 = forbidden -> proibido (login inválido)
        }


        return 'login';
    }

    public function logout()
    {
        return 'logout';
    }

    public function refresh()
    {
        return 'refresh';
    }

    public function me()
    {
        return 'me';
    }
}
