<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/getPlayers', 'PlayersController@show');
Route::post('/newPlayer', 'PlayersController@newPlayer');
Route::get('/getPlayerId/{idPlayer}', 'PlayersController@getPlayerId');
Route::put('/editPlayer', 'PlayersController@editPlayer');
Route::post('/confirmPresence/{idplayer}/{presence}', 'PlayersController@confirmPresence');
Route::get('/randomDraw/{playerTeam}', 'PlayersController@randomDraw');
