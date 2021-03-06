<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,' . $this->id . '|min:3',
            /*  'imagem' => 'required|file|mimes:png,docx,xlsx,pdf,ppt,jpeg,mp3' */
            'imagem' => 'required|file|mimes:png'
        ];

        /**
         * Parâmetros do método rules() em "unique":
         * 1) tabela
         * 2) nome da coluna que será pesquisada na tabela
         * 3) id do registro que será desconsiderado
         *
         */
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres'
        ];
    }

    //Relacionamento de MARCA com MODELO
    //No plural, porque uma MARCA pode ter vários MODELOS
    public function modelos()
    {
        //Uma MARCA possui muitos MODELOS
        //Então retorna um array.
        return $this->hasMany('App\Models\Modelo');
    }
}
