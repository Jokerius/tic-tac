<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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

Route::get('/v1/games', [
    'uses' => 'GameController@index',
]);

Route::post('/v1/games', [
    'uses' => 'GameController@create',
]);

Route::get('/v1/games/{game_id}', [
    'as' => 'Show Game',
    'uses' => 'GameController@show',
]);

Route::put('/v1/games/{game_id}', [
    'uses' => 'GameController@update',
]);

Route::delete('/v1/games/{game_id}', [
    'uses' => 'GameController@delete',
]);
