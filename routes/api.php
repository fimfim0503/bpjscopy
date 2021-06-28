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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');
Route::get('book', 'BookController@book');
Route::post('namahari', 'UserController@namahari');

//  Route::post('getlistjadwaloperasi', 'ListjadwalOp@Listjadwaoperasi');
// Route::post('getnoantrian', 'GetNoAntrian@Antrianbpjs');




//test get form other app
Route::get('apiget', 'Testapifromother@index');

Route::get('bookall', 'BookController@bookAuth')->middleware('jwt.verify');
Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify');
Route::post('getnoantrian', 'GetNoAntrian@Antrianbpjs')->middleware('jwt.verify');
Route::post('statusantrian', 'StatusAntrian@statusantrian')->middleware('jwt.verify');
Route::post('sisaantrian', 'StatusAntrian@sisaantrian')->middleware('jwt.verify');
Route::post('batalantrian', 'StatusAntrian@batalantrian')->middleware('jwt.verify');
Route::post('checkin', 'StatusAntrian@checkin')->middleware('jwt.verify');
Route::post('getrekapantrian', 'Rekapantrian@rekapantrian')->middleware('jwt.verify');
Route::post('getlistjadwaloperasi', 'UserController@Listjadwaoperasi')->middleware('jwt.verify');
Route::post('getlistkodebookingoperasi', 'UserController@Kodebookingoperasi')->middleware('jwt.verify');