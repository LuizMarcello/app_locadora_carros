<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
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
        /* $marcas = Marca::all(); */
        $marca = $this->marca->all();
        return $marca;
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
        /* $marca = Marca::create($request->all()); */
        $marca = $this->marca->create($request->all());
        /* dd($marca); */
        /* dd($request->all()); */
        /* return 'Chegamos até aqui (Store)'; */
        return $marca;
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
        $marca = $this->marca->find($id);
        return $marca;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marca $marca)
    {
       /* return 'Chegamos até aqui (Update)'; */
       /* print_r($request->all()); */ //Os dados atualizados do "body" da requisição, deste id.
      /*  echo '<hr>'; */
       /* print_r($marca->getAttributes()); */ //Os dados antigos do objeto instanciado, deste id.
       $marca->update($request->all());
       return $marca;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marca $marca)
    {
        /* print_r($marca->getAttributes()); */
        /* return 'Chegamos até aqui (delete)'; */
        // Recuperando o objeto(id), cuja instância(Model Marca) veio no parâmetro
        // e executando o método "delete()":
        $marca->delete();
        return ['msg'=>'A marca foi removida com sucesso']; //Retornando um array associativo.
    }
}
