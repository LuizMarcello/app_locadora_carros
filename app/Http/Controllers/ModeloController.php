<?php

/* php artisan storage:link - Cria um link simbólico entre:
   storage/app/public/imagens e public/storage/imagens  */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    //Uma outra forma de lidar com a manipulação dos models dentro dos controllers
    //Construtor injetando uma instância do model(um objeto) nesse controller
    //"Type Hinting"(com parâmetro "tipado")
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Assim usando o método estático all() */
        /* $modelo = Modelo::all(); */
        /* Agora acessando o método de "um objeto" */
        /* with():Adicionando o relacionamento deste modelo com MARCA */
        $modelo = $this->modelo->with('marca')->get();
        //Com o método all(): Criando um obj de consulta + get() = collection
        //Com o método get(): Modificar a consulta -> collection

        /* Usando o helper "response()", para modificar os detalhes da resposta do
            status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
        return response()->json($modelo, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        /* $modelo = Modelo::create($request->all()); */

        //nome
        //imagem

        /* Conceito do "Accept": Uma requisição enviada no cabeçalho de uma requisição feita
           pela client(Em uma api webService rest feita em láravel), mudando o comportamento
           padrão em função da forma como o "validade()" trabalha(no "Headers" da requisição,
           mudando o retôrno para "application/json")  */
        $request->validate($this->modelo->rules());

        //dd($request->nome); //Acessando o atributo/input 'nome'.
        //dd($request->get('nome')); //Acessando o atributo/input 'nome', usando o 'get()'.
        //dd($request->input('nome')); //Acessando o atributo/input 'nome', usando o método 'input()'.

        //dd($request->imagem); //Acessando o atributo/input 'imagem'
        $imagem = $request->file('imagem'); //Acessando o atributo/input 'imagem',
        //ou array de imagens, usando o método 'file()'.

        //Variável receberá o retôrno do método "store()", que serão o nome,
        //a extensão e o path os quais esta imagem foi armazenada.
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        /* Agora acessando o método de "um objeto" */
        //Persistindo no banco
        //1ª síntaxe:
        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs

        ]);
        //ou assim (2ª síntaxe):
        //$modelo->nome = $request->nome;
        //$modelo->imagem = $imagem_urn;
        //$modelo->save();

        /* dd($modelo); */
        /* dd($request->all()); */
        /* return 'Chegamos até aqui (Store)'; */
        /* Usando o helper "response()", para modificar os detalhes da resposta do
              status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /* Acessando o método de "um objeto" */
        /* with():Adicionando o relacionamento deste modelo com MARCA */
        $modelo = $this->modelo->with('marca')->find($id);
        /* Validando: */
        if ($modelo === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* return ['êrro' => 'O recurso pesquisado não existe!']; */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso pesquisado não existe!'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /* print_r($request->all()); */ //Os dados "atualizados" do "body" da requisição, deste id.
        /* print_r($modelo->getAttributes()); */ //Os dados "antigos" do objeto instanciado, deste id.
        /* Acessando o método de "um objeto" */
        /* Os verbos PATH E PUT, quando usados em conjunto com o "form-data", não são
           reconhecidos pelo láravel(uma limitação).Então acrescentar no Body:
           key:_method e value:put(ou path). */
        $modelo = $this->modelo->find($id);

        /* Validando: */
        if ($modelo === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
                  status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso a ser atualizado não existe!'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //Percorrendo todas as regras definidas no Model
            foreach ($modelo->rules() as $input => $regra) {
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
            //"feedback()" do Model modelo.php, para validar as regras no update.
            $request->validate($regrasDinamicas);
        } else {
            //Validação: Função "validate()". Como parâmetros os métodos "rules()" e
            //"feedback()" do Model modelo.php, para validar as regras no update.
            $request->validate($modelo->rules());
        }

        //Remove o arquivo antigo, caso um novo arquivo tenha sido enviado no request.
        if ($request->file('imagem')) {
            //"Storage" importado acima com "use"(Facades).
            Storage::disk('public')->delete($modelo->imagem);
        }

        //Acessando o atributo/input 'imagem'
        //ou array de imagens, usando o método 'file()'.
        //Recuperando o objeto "imagem"
        $imagem = $request->file('imagem');

        //Variável receberá o retôrno do método "store()", que serão o nome,
        //a extensão e o path, os quais esta imagem foi armazenada.
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        //Usando o método fill() para sobrepôr os valores deste objeto
        //com base no $request->all() (array de parâmetros).
        $modelo->fill($request->all());
        
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        //Agora acessando o método de um objeto
        //e atualizando no banco
        /* $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs

        ]); */
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* print_r($modelo->getAttributes()); */
        // Recuperando o objeto(seu id), cuja instância(Model modelo) veio no parâmetro
        /* Acessando o método de "um objeto" */
        $modelo = $this->modelo->find($id);
        /* Validando: */
        if ($modelo === null) {  /* operador idêntico "===": mesmo tipo e valor */
            /* Usando o helper "response()", para modificar os detalhes da resposta do
               status code http, que será dada pelo laravel. Como 2º parâmetro, o código http */
            return response()->json(['êrro' => 'O recurso a ser excluido não existe!'], 404);
        }
        //Usando o "façade Storage" importado acima com "use"(Facades).
        //Remove o arquivo de "imagem" antigo (coluna "imagem") do id.
        Storage::disk('public')->delete($modelo->imagem);

        /* Executando o método "delete()": */
        //Deleta todo o registro referente ao id
        $modelo->delete();
        //Retornando um array associativo.
        return response()->json(['msg' => 'O modelo foi removido com sucesso'], 200);
    }
}
