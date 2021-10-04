<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//Mapeamento automático da rota, para todos os métodos do controlador que foi criado
//com "resource", que já cria todos os métodos padrão.
//Usando "resource" criam as rotas para index, store, create, show, update, destroy e edit no controller.
/* Route::resource('cliente'  'App\Http\Controllers\ClienteController'); */

//Usando "apiResource" só criam as rotas para index, store, show, update, e destroy no controller.
//Para "api", as rotas para "create" e "edit" não tem necessidade(eles criam um formulários).
//Grupo de rotas protegido com prefix de versionamento da api. Agora este prefix(versão da api)
//também terá que ser acrescentado nas urls.
//Grupo de rotas também utilizando o helper/middleware "jwt.auth" para autenticação.
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::post('me', 'AuthController@me');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('logout', 'AuthController@logout');
    Route::apiResource('cliente', 'ClienteController');
    Route::apiResource('carro', 'CarroController');
    Route::apiResource('locacao', 'LocacaoController');
    Route::apiResource('marca', 'MarcaController');
    Route::apiResource('modelo', 'ModeloController');
});

//Rotas públicas.
Route::post('login', 'AuthController@login');
