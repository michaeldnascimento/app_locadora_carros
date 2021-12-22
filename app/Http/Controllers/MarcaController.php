<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\AcceptHeader;

class MarcaController extends Controller
{

    //Injecão de tipo
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
        //$marcas = Marca::all();
        $marcas = $this->marca->all();
        return response()->json($marcas, 200);
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
        //
        //$marca = Marca::create($request->all());
//        $regras = [
//            'nome' => 'required|unique:marcas',
//            'imagem' => 'required'
//        ];
//
//        $feedback = [
//            'required' => 'O campo :attribute é obrigatório',
//            'nome.unique' => 'O nome da marca já existe'
//        ];

        //$request->validate($regras, $feedback);
        $request->validate($this->marca->rules(), $this->marca->feedback());

        //stateless

        //dd(request->nome);
        //dd(request->get('nome');
       //dd(request->input('nome');


        $marca = $this->marca->create($request->all());
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); //
        }
        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe, impossivel realizar a atualizacão!'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            //precorrendo todas as regras definidas no model


            foreach ($marca->rules() as $input => $regras){
                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regras;
                }
            }

        }else{
            $request->validate($marca->rules(), $marca->feedback());
        }


        $marca->update($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'o recurso pesquisado não existe, impossivel realizar a exclusão'], 404);
        }

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
