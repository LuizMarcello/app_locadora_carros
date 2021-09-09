<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules()
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png,jpg,pdf,,jpeg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean', //true, false, 1, 0, "1", "0"
            'abs' => 'required|boolean' //true, false, 1, 0, "1", "0"

        ];

        /**
         * Parâmetros do método rules() em "unique":
         * 1) tabela
         * 2) nome da coluna que será pesquisada na tabela
         * 3) id do registro que será desconsiderado
         *
         */
    }
}
