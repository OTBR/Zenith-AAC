<?php

$controller_path = 'App\\Modules\\Server\\Controllers\\';
Route::get('account/create', array('as' => 'account.create', 'before' => 'guest', 'uses' => $controller_path.'AccountsController@create'));
Route::post('account', array('as' => 'account.store', 'before' => 'guest', 'uses' => $controller_path.'AccountsController@store'));
Route::get('account', array('as' => 'account.show', 'before' => 'auth', 'uses' => $controller_path.'AccountsController@show'));
Route::put('account', array('as' => 'account.update', 'before' => 'auth', 'uses' => $controller_path.'AccountsController@update'));
Route::delete('account', array('as' => 'account.destroy', 'before' => 'auth', 'uses' => $controller_path.'AccountsController@destroy'));

Route::get('character', array('as' => 'character.index', 'uses' => $controller_path.'CharactersController@index'));
Route::get('online/{order?}', array('as' => 'character.online', 'uses' => $controller_path.'CharactersController@online'));
Route::get('character/create', array('as' => 'character.create', 'before' => 'auth', 'uses' => $controller_path.'CharactersController@create'));
Route::post('character', array('as' => 'character.store', 'before' => 'auth', 'uses' => $controller_path.'CharactersController@store'));
Route::get('character/{name}', array('as' => 'character.show', 'uses' => $controller_path.'CharactersController@show'));
Route::get('character/{name}/edit', array('as' => 'character.edit', 'before' => 'auth', 'uses' => $controller_path.'CharactersController@edit'));
Route::put('character/{name}', array('as' => 'character.update', 'before' => 'auth', 'uses' => $controller_path.'CharactersController@update'));

Route::get('highscores/{skill?}', array('as' => 'highscores.show', 'uses' => $controller_path.'HighscoresController@show'));

Route::get('houses', array('as' => 'house.index', 'uses' => $controller_path.'HousesController@index'));
Route::get('houses/{id}', array('as' => 'house.show', 'uses' => $controller_path.'HousesController@show'));

Route::get('houses/{id}/bid', array('as' => 'housebid.create', 'before' => 'auth', 'uses' => $controller_path.'HouseBidsController@create'));
Route::post('houses/{id}/bid', array('as' => 'housebid.store', 'before' => 'auth', 'uses' => $controller_path.'HouseBidsController@store'));
