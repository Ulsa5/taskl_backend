<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Rutas que pueden ser accedidas sin proveer un token valido
Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/register', 'App\Http\Controllers\AuthController@register');

//Rutas que no pueden ser accedidas sin proveer un token valido
Route::group(['middleware' => 'auth.jwt'], function(){
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
});