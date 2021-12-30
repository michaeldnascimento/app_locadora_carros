<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function index(Request $request)
    {

        //-----------MODELO ULTILIZANDO O REPOSITORY
        $marcaRepository = new MarcaRepository($this->marca);

        //RECUPERANDO OS ATRIOBUTOS Modelos
        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        //recuperando os atributos
        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);
        }

        return response()->json($marcaRepository->getResultado(), 200);


        /** ------------------MODELO PADRÃO----------------------------------------------
        $marcas = array();

        //RECUPERANDO OS ATRIOBUTOS MARCA
        if($request->has('atributos_modelos')){
            $atributos_modelos = $request->atributos_modelos;
            $marcas =  $this->marca->with('modelos:id,'.$atributos_modelos);
        } else {
            $marcas = $this->marca->with('modelos');
        }

        //WHERE
        if($request->has('filtro')) {

            //SEPARANDO OS WHERES
            $filtros = explode(';', $request->filtro);
            foreach($filtros as $key => $condicao){

                $c = explode(':', $condicao);
                $marcas = $marcas->where($c[0], $c[1], $c[2]);

            }

        }

        //recuperando os atributos
        if($request->has('atributos')){
            $atributos = $request->atributos;
            $marcas =  $marcas->selectRaw($atributos)->get();
        } else {
            $marcas = $marcas->get();
        }

        //$marcas = Marca::all();
        //$marcas = $this->marca->all();
        //$marcas = $this->marca->with('modelos')->get();
        return response()->json($marcas, 200);

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

        //amarzenando a imagem
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        //dd($imagem_urn);



        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        //OU
//        $marca->nome = $request->nome;
//        $marca->imagem = $imagem_urn;
//        $marca->save();

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
        $marca = $this->marca->with('modelos')->find($id);
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

        //remove o arquivo antigo com o Facedes\Storage
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        //amarzenando a imagem
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        //dd($imagem_urn);

        //preencher o objeto $marca com os dados do request
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;


        /**
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
         * */

        //ULTILIZANDO O METHOD SAVE
        $marca->save();

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

        //remove o arquivo antigo com o Facedes\Storage
        Storage::disk('public')->delete($marca->imagem);



        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
