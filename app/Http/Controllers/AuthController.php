<?php

namespace App\Http\Controllers;

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
        //Para revogar uma autorização(um jwt token), é preciso ter
        //uma autorização ainda válida, ou seja, encaminhar um token jwt válido.
        //O tokem é colocado na "blacklist".
        auth('api')->logout();
        //Retorna um array
        return response()->json(['msg' => 'Logout realizado com sucesso!']);
    }

    //Renovando a atuorização(token JWT)
    public function refresh()
    {
        //Para este método "refresh()" funcionar, é necessário que,
        //em uma requisição pára esta rota "refresh", o cliente
        //encaminhe um token jwt "válido". Só é possível renovar
        //uma autorização de acesso, se o cliente solicitante tiver
        //uma autorização ainda válida, ou seja, enviar um tokem jwt válido
        $token = auth('api')->refresh();
        //Retornandp um array ("[]"), com o token já renovado.
        return response()->json(['token' => $token]);
    }

    public function me()
    {
        //Serão fornecidos os dados do usuário para o qual o tokem jwt
        //foi liberado, que foi autenticado, e recebeu autorização.
        return response()->json(auth()->user());
    }
}
