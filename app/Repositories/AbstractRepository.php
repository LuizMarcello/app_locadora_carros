<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /* Métodos que vão definir as regras de negócios comuns
        para os Repositories específicos, que vão herdar desta. */
    public function selectAtributosRegistrosRelacionados($atributos)
    {
        //A query está sendo montada
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtros)
    {
        $filtros = explode(';', $filtros);
        foreach ($filtros as $key => $condicao) {

            $c = explode(':', $condicao);
            //A query está sendo montada.
            $this->model =  $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectAtributos($atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado()
    {
        return $this->model->get();
    }
}
