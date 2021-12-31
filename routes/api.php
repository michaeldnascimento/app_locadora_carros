<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function (){
    return ['Chegamos aqui' => 'SIM'];
});

//PREFIX PARA DEFINIR A VERSÃO E MIDDLEWARE PARE PROTEGER A ROTA E GROUP PARA AGRUPAR TODAS AS ROTAS
Route::prefix('v1')->middleware('jwt.auth')->group(function() {
//Route::resource('cliente', 'App\Http\Controllers\ClienteController');
    Route::apiResource('cliente', 'App\Http\Controllers\ClienteController'); // para trabalhar com api Sem created e edit
    Route::apiResource('carro', 'App\Http\Controllers\CarroController'); //
    Route::apiResource('locacao', 'App\Http\Controllers\LocacaoController'); //
    Route::apiResource('marca', 'App\Http\Controllers\MarcaController'); //
    Route::apiResource('modelo', 'App\Http\Controllers\ModeloController'); //
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh'); // rota de refresh do token
    Route::post('me', 'App\Http\Controllers\AuthController@me'); // rota de consulta do usuario do token
    Route::post('logout', 'App\Http\Controllers\AuthController@logout'); // rota para invalidar o token
});

//ROTA DE LOGIN E TOKEN
Route::post('login', 'App\Http\Controllers\AuthController@login');
