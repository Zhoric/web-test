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


Route::group(['prefix' => 'api'], function(){

    Route::group(['prefix' => 'org'], function (){
        Route::get('institute/profiles/{id}', 'OrgStructureController@getInstituteProfiles');
        Route::get('profile/groups/{id}', 'OrgStructureController@getProfileGroups');
        Route::get('profile/plans/{id}', 'OrgStructureController@getInstituteProfiles');
        Route::get('institutes', 'OrgStructureController@getAllInstitutes');
        Route::post('profile/create', 'OrgStructureController@createProfile');
        Route::post('profile/update', 'OrgStructureController@updateProfile');
        Route::post('profile/delete/{id}', 'OrgStructureController@deleteProfile');
    });

});
