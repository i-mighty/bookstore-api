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
Route::get('login', function (){
    response()->json(['status' => 'error', 'message' => 'Please login to access that service']);
})->name('login');
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
//Book Resource Route
Route::resource('books', 'BookController');
Route::post('books/{id}/rate', 'BookController@rate');
Route::post('books/{id}/comment', 'BookController@comment');


