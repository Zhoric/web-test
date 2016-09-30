<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::get('/','HomeController@index');

Route::get('/demo','DemoController@index');
Route::get('/docker','DemoController@docker');
Route::get('editor','DemoController@editor');
Route::get('getProfiles', 'DemoController@getProfiles');
Route::get('profiles',function (){
   return View('admin.index');
});

Auth::routes();


