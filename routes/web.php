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
Route::get('editor','DemoController@editor');
Route::get('getProfiles', 'DemoController@getProfiles');
Route::get('test', 'DemoController@index');

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('welcome',function (){
   return View('welcome');
});

Route::get('admin/main', function(){return View('admin.main');});
Route::get('/api/org/profilesOf/{id}', 'OrgStructureController@getInstituteProfiles');
Route::resource('/api/org/institutes', 'OrgStructureController');

Route::get('/api/group/byProfile/{id}', 'GroupController@getProfileGroups');
Route::resource('/api/group/', 'GroupController');