<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected  $fillable = ['nome', 'imagem'];

    public function rules(): array
    {

        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg'
        ];
        /**
         * unique
         * 1) tabela
         * 2) nome da coluna que será pesquisada na tabela
         * 3) id do registro que será desconsuiderado na pesquisa
         */
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres'
            ];
    }

    public function modelos()
    {
        //UMA MARCA POSSUI MUITOS MODELOS
        return $this->hasMany('App\Models\Modelo');
    }
}
