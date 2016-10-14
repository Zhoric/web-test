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


Route::group(['prefix' => 'api'], function() {

    /*--------------------------------------------------------------------------------
     *      Организационная структура ВУЗа (Институты, профили)
     * -------------------------------------------------------------------------------
     */
    Route::get('institute/{id}/profiles', 'OrgStructureController@getInstituteProfiles');
    Route::get('institutes', 'OrgStructureController@getAllInstitutes');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('{id}/groups', 'OrgStructureController@getProfileGroups');
        Route::get('{id}/plans', 'OrgStructureController@getProfilePlans');

        Route::post('create', 'OrgStructureController@createProfile');
        Route::post('update', 'OrgStructureController@updateProfile');
        Route::post('delete/{id}', 'OrgStructureController@deleteProfile');
    });


    /*------------------------------------------------------------------------------
     *              УЧЕБНЫЕ ПЛАНЫ
     * -----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'plan'], function () {
        Route::get('{id}', 'StudyPlanController@getPlan');
        Route::get('{id}/disciplines', 'StudyPlanController@getPlanDisciplines');
        Route::post('create', 'StudyPlanController@create');
        Route::post('update', 'StudyPlanController@update');
        Route::post('delete/{id}', 'StudyPlanController@delete');

        Route::group(['prefix' => 'discipline'], function () {
            Route::get('{id}/marks', 'StudyPlanController@getDisciplinePlanMarkTypes');
            Route::post('create', 'StudyPlanController@addDisciplinePlan');
            Route::post('update', 'StudyPlanController@updateDisciplinePlan');
            Route::post('delete/{id}', 'StudyPlanController@deleteDisciplinePlan');
        });

        Route::group(['prefix' => 'mark'], function () {
            Route::post('create', 'StudyPlanController@addDisciplinePlan');
            Route::post('update', 'StudyPlanController@updateDisciplinePlan');
            Route::post('delete/{id}', 'StudyPlanController@deleteDisciplinePlan');
        });
    });

    /*-----------------------------------------------------------------------------
     *                           ГРУППЫ
     *-----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'groups'], function(){
        Route::get('show', 'GroupController@getProfileGroupsByNamePaginated');
        Route::get('{id}', 'GroupController@getGroup');
        Route::post('create', 'GroupController@create');
        Route::post('update', 'GroupController@update');
        Route::post('delete/{id}', 'GroupController@delete');
        Route::get('/', 'GroupController@getAll');
        Route::get('{id}/students', 'GroupController@getGroupStudents');

        /*------------------------------------------------------------------------
        *                      Работа со студентами группы                       */
        Route::group(['prefix' => 'student'], function () {

            Route::post('create', 'GroupController@createStudent');
            Route::post('update', 'GroupController@updateStudent');
            Route::post('delete/{id}', 'GroupController@deleteStudent');
            Route::post('setGroup', 'GroupController@setStudentGroup');
        });
    });


});


