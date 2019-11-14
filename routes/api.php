<?php

use Illuminate\Http\Request;

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


Route::group(['middleware' => 'cors'], function () {

    Route::post('_Log_In', 'Auth\LoginController@Login');

    Route::post('_Sexo_Datos', 'SexoController@all');
    Route::post('_Sexo_Xid', 'SexoController@show');
    Route::post('_Sexo_Agregar', 'SexoController@add');
    Route::post('_Sexo_Editar', 'SexoController@edit');
    Route::post('_Sexo_Eliminar', 'SexoController@delete');

    Route::post('_Usuario_Datos', 'UsuarioController@all');
    Route::post('_Usuario_Xid', 'UsuarioController@show');
    Route::post('_Usuario_Agregar', 'UsuarioController@add');
    Route::post('_Usuario_Editar', 'UsuarioController@edit');
    Route::post('_Usuario_Eliminar', 'UsuarioController@delete');

});

