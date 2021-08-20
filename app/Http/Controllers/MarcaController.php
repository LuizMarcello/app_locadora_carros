<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    //Uma outra forma de lidar com a manipulação dos models dentro dos controllers
    //Construtor injetando uma instância do model(um objeto) nesse controller
    //"Type Hinting"(com parâmetro "tipado")
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Assim usando o método estático all() */
        /* $marcas = Marca::all(); */
        /* Agora acessando o método de "um objeto" */
        $marca = $this->marca->all();
        /* Usando o helper "response()", para modificar os detalhes da resposta do
            status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
        return response()->json($marca, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* De um modo "massivo" usando o "Model": */
        /* Na verdade, "$request->all()" é(retorna)um array associativo */
        /* Assim usando o método estático create() */
        /* $marca = Marca::create($request->all()); */

        //nome
        //imagem

        /* Conceito do "Accept": Uma requisição enviada no cabeçalho de uma requisição feita
           pela client(Em uma api webService rest feita em láravel), mudando o comportamento
           padrão em função da forma como o "validade()" trabalha(no "Headers" da requisição,
           mudando o retôrno para "application/json")  */
        $request->validate($this->marca->rules(), $this->marca->feedback());

        /* Agora acessando o método de "um objeto" */
        $marca = $this->marca->create($request->all());
        /* dd($marca); */
        /* dd($request->all()); */
        /* return 'Chegamos até aqui (Store)'; */
        /* Usando o helper "response()", para modificar os detalhes da resposta do
           status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    /* public function show(Marca $marca) */
    public function show($id)
    {
        /* Acessando o método de "um objeto" */
        $marca = $this->marca->find($id);
        /* Validando: */
        if ($marca === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* return ['êrro' => 'O recurso pesquisado não existe!']; */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso pesquisado não existe!'], 404);
        }
        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    /* public function update(Request $request, Marca $marca) */
    public function update(Request $request, $id)
    {
        /* print_r($request->all()); */ //Os dados "atualizados" do "body" da requisição, deste id.
        /* print_r($marca->getAttributes()); */ //Os dados "antigos" do objeto instanciado, deste id.
        /* Acessando o método de "um objeto" */
        $marca = $this->marca->find($id);
        /* Validando: */
        if ($marca === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso a ser atualizado não existe!'], 404);
        }

        //Validação: Função "validate()". Como parâmetros os métodos "rules()" e
        //"feedback()" do Model Marca.php, para validar as regras no update.
        $request->validate($marca->rules(), $marca->feedback());
        $marca->update($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* print_r($marca->getAttributes()); */
        // Recuperando o objeto(seu id), cuja instância(Model Marca) veio no parâmetro
        /* Acessando o método de "um objeto" */
        $marca = $this->marca->find($id);
        /* Validando: */
        if ($marca === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso a ser excluido não existe!'], 404);
        }
        /* Executando o método "delete()": */
        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso'], 200) ; //Retornando um array associativo.
    }
}
