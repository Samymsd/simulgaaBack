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



Route::resource('user', 'UserController');
Route::resource('rol', 'RolController');


//Login
Route::post('login', 'UserController@login');




//Reunion
Route::post('agenda/reunion/create', 'ReunionController@store');
Route::get('agenda/reunion/{user_id}', 'ReunionController@show');
Route::get('agenda/reunion/participaciones/{user_id}', 'ReunionController@showParticipaciones');
Route::get('agenda/reunion/creaciones/{user_id}', 'ReunionController@showCreaciones');
Route::get('agenda/reunion/historico/{user_id}', 'ReunionController@showHistorico');
Route::get('agenda/reunion/creacionesPersonales/{user_id}', 'ReunionController@showCreacionesPersonales');
Route::put('agenda/reunion/update/{id}', 'ReunionController@update');

Route::post('agenda/reunion/participacion', 'ReunionController@updateAsistencia');

