<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\AcceptHeader;

class ModeloController extends Controller
{

    //Injecão de tipo
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //-----------MODELO ULTILIZANDO O REPOSITORY
        $modeloRepository = new ModeloRepository($this->modelo);

        //RECUPERANDO OS ATRIOBUTOS Modelos
        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        //recuperando os atributos
        if($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributos);
        }

        return response()->json($modeloRepository->getResultado(), 200);

        /** ------------------MODELO PADRÃO----------------------------------------------
        $modelos = array();

        //RECUPERANDO OS ATRIOBUTOS MARCA
        if($request->has('atributos_marca')){
            $atributos_marca = $request->atributos_marca;
            $modelos =  $this->modelo->with('marca:id,'.$atributos_marca);
        } else {
            $modelos = $this->modelo->with('marca');
        }

        //WHERE
        if($request->has('filtro')) {

          //SEPARANDO OS WHERES
          $filtros = explode(';', $request->filtro);
          foreach($filtros as $key => $condicao){

               $c = explode(':', $condicao);
               $modelos = $modelos->where($c[0], $c[1], $c[2]);

          }

        }

        //recuperando os atributos
        if($request->has('atributos')){
            $atributos = $request->atributos;
            $modelos =  $modelos->selectRaw($atributos)->get();
        } else {
            $modelos = $modelos->get();
        }

        //all() -> criando um obj de consulta + get() = collection
        //get() -> modificar a consulta -> collection
        //$modelo = $this->modelo->with('marca')->get();
        return response()->json($modelos, 200);
         *
         */
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

        $request->validate($this->modelo->rules());

        //amarzenando a imagem
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //ADICIONANDO O RELACIONAMENTO DE MODELO COM MARCA ULTILIZANDO O WITH('MARCA')
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); //
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
     * @param integer $id
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe, impossivel realizar a atualizacão!'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            //precorrendo todas as regras definidas no model


            foreach ($modelo->rules() as $input => $regras){
                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regras;
                }
            }

        }else{
            $request->validate($modelo->rules());
        }

        //remove o arquivo antigo com o Facedes\Storage
        if($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        //amarzenando a imagem
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        //dd($imagem_urn);
        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        /*
        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        **/

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'o recurso pesquisado não existe, impossivel realizar a exclusão'], 404);
        }

        //remove o arquivo antigo com o Facedes\Storage
        Storage::disk('public')->delete($modelo->imagem);



        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removida com sucesso!'], 200);
    }
}
