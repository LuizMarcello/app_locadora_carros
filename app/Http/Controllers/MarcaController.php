<?php

/* php artisan storage:link - Cria um link simbólico entre:
   storage/app/public/imagens e public/storage/imagens  */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
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


        //dd($request->nome); //Acessando o atributo/input 'nome'.
        //dd($request->get('nome')); //Acessando o atributo/input 'nome', usando o 'get()'.
        //dd($request->input('nome')); //Acessando o atributo/input 'nome', usando o método 'input()'.

        //dd($request->imagem); //Acessando o atributo/input 'imagem'
        $imagem = $request->file('imagem'); //Acessando o atributo/input 'imagem',
        //ou array de imagens, usando o método 'file()'.

        //Variável receberá o retôrno do método "store()", que serão o nome,
        //a extensão e o path os quais esta imagem foi armazenada.
        $imagem_urn = $imagem->store('imagens', 'public');

        /* Agora acessando o método de "um objeto" */
        //Persistindo no banco
        //1ª síntaxe:
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        //ou assim (2ª síntaxe):
        //$marca->nome = $request->nome;
        //$marca->imagem = $imagem_urn;
        //$marca->save();

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
     * @param  \Illuminate\Http\Request $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    /* public function update(Request $request, Marca $marca) */
    public function update(Request $request, $id)
    {
        /* print_r($request->all()); */ //Os dados "atualizados" do "body" da requisição, deste id.
        /* print_r($marca->getAttributes()); */ //Os dados "antigos" do objeto instanciado, deste id.
        /* Acessando o método de "um objeto" */
        /* Os verbos PATH E PUT, quando usados em conjunto com o "form-data", não são
           reconhecidos pelo láravel(uma limitação).Então acrescentar no Body:
           key:_method e value:put(ou path). */
        $marca = $this->marca->find($id);

        /* Validando: */
        if ($marca === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso a ser atualizado não existe!'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //Percorrendo todas as regras definidas no Model
            foreach ($marca->rules() as $input => $regra) {
                /* $teste .= 'Input: ' . $input . ' | Regra: ' . $regra . '<br>'; */

                //Coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH.
                //"array_key_exists()": Método nativo do php para pesquisar arrays. Pesquisando
                //input(1º parâmetro)no request->all(2º parâmetro), que é um array com os parâmetros
                //enviados na requisição "$request".
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            //Validação: Função "validate()". Como parâmetros os métodos "rules()" e
            //"feedback()" do Model Marca.php, para validar as regras no update.
            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            //Validação: Função "validate()". Como parâmetros os métodos "rules()" e
            //"feedback()" do Model Marca.php, para validar as regras no update.
            $request->validate($marca->rules(), $marca->feedback());
        }

        //Remove o arquivo antigo, caso um novo arquivo tenha sido enviado no request.
        if ($request->file('imagem')) {
            //"Storage" importado acima com "use"(Facades).
            Storage::disk('public')->delete($marca->imagem);
        }

        //Acessando o atributo/input 'imagem'
        //ou array de imagens, usando o método 'file()'.
        //Recuperando o objeto "imagem"
        $imagem = $request->file('imagem');

        //Variável receberá o retôrno do método "store()", que serão o nome,
        //a extensão e o path, os quais esta imagem foi armazenada.
        $imagem_urn = $imagem->store('imagens', 'public');

        //Agora acessando o método de um objeto
        //e atualizando no banco
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
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
        //Usando o "façade Storage" importado acima com "use"(Facades).
        //Remove o arquivo de "imagem" antigo (coluna "imagem") do id.
        Storage::disk('public')->delete($marca->imagem);

        /* Executando o método "delete()": */
        //Deleta todo o registro referente ao id
        $marca->delete();
        //Retornando um array associativo.
        return response()->json(['msg' => 'A marca foi removida com sucesso'], 200);
    }
}
