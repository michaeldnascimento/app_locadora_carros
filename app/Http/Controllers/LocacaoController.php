<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocacaoRequest;
use App\Http\Requests\UpdateLocacaoRequest;
use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{

    //Injecão de tipo
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //-----------MODELO ULTILIZANDO O REPOSITORY
        $locacaoRepository = new LocacaoRepository($this->locacao);


        if($request->has('filtro')) {
            $locacaoRepository->filtro($request->filtro);
        }

        //recuperando os atributos
        if($request->has('atributos')){
            $locacaoRepository->selectAtributos($request->atributos);
        }

        return response()->json($locacaoRepository->getResultado(), 200);
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
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate($this->locacao->rules());

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);


        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); //
        }
        return response()->json($locacao, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function edit(Locacao $locacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $locacao = $this->locacao->find($id);

        if($locacao === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe, impossivel realizar a atualizacão!'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            //precorrendo todas as regras definidas no model


            foreach ($locacao->rules() as $input => $regras){
                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regras;
                }
            }

        }else{
            $request->validate($locacao->rules());
        }

        //preencher o objeto $marca com os dados do request
        $locacao->fill($request->all());
        //ULTILIZANDO O METHOD SAVE
        $locacao->save();

        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $locacao = $this->locacao->find($id);

        if($locacao === null){
            return response()->json(['erro' => 'o recurso pesquisado não existe, impossivel realizar a exclusão'], 404);
        }


        $locacao->delete();
        return response()->json(['msg' => 'A locaćão foi removida com sucesso!'], 200);
    }
}
