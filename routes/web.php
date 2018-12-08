<?php


use TestEngine\TestSessionTracker;

Route::get('/','HomeController@index');
Route::get('editor','DemoController@editor');
Route::get('docker','DemoController@docker');
Route::get('getProfiles', 'DemoController@getProfiles');
Route::get('test', 'DemoController@index');
Route::get('auth', 'DemoController@auth');
Route::get('receiveCode','DemoController@receiveCode');
Route::get('compile','DemoController@compileOnDocker');
Route::post('register/checkEmail', 'Auth\RegisterController@checkIfEmailExists');
Route::get('role','UserController@getRoleByUser');
Route::get('cachetest','DemoController@test');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');



Route::get('/', 'HomeController@index');

Route::get('welcome',function (){
   return View('welcome');
});

Route::get('/test', function(){return View('student.test');});
Route::get('/home', function(){return View('student.home');})
    ->middleware('checkRole:'.UserRole::Student);
Route::get('/results', function(){return View('student.results');})
    ->middleware('checkRole:'.UserRole::Student);
Route::get('/discipline/{id}', function(){return View('student.discipline');})
    ->middleware('checkRole:'.UserRole::Student);
Route::get('/media/{id}', function(){return View('student.media');})
    ->middleware('checkRole:'.UserRole::Student);
Route::get('/materials', function(){return View('student.materials');})
    ->middleware('checkRole:'.UserRole::Student);


Route::group(['prefix' => 'admin','middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
    function(){
    Route::get('/', function(){return View('admin.main');});
    Route::get('main', function(){return View('admin.main');});
    Route::get('students', function(){return View('admin.students');});
    Route::get('students/{id}', function(){return View('admin.students');});
    Route::get('lecturers', function(){return View('admin.lecturers');});
    Route::get('groups', function(){return View('admin.groups');});
    Route::get('disciplines', function(){return View('admin.disciplines');});
    Route::get('groups/{id}', function(){return View('admin.groups');});
    Route::get('theme/{id}', function(){return View('admin.themes');});
    Route::get('tests/{id}', function(){return View('admin.tests');});
    Route::get('tests', function(){return View('admin.tests');});
    Route::get('editor/anchor/{id}', function(){return View('admin.editor');});
    Route::get('editor/{id}', function(){return View('admin.editor');});
    Route::get('studyplans', function(){return View('admin.studyplans');});
    Route::get('performance', function(){return View('admin.performance');});
    Route::get('studyplan/{id}', function(){return View('admin.studyplan');});
    Route::get('institutes', function(){return View('admin.institutes');});
    Route::get('manual', function(){return View('admin.manual');});
    Route::get('manualSections', function(){return View('admin.manualSections');});
    Route::get('results', function(){return View('admin.results');});
    Route::get('overallresults', function(){return View('admin.overall');});
    Route::get('result/{id}', function(){return View('admin.result');});
    Route::get('monitoring', function(){return View('admin.monitoring');});
    Route::get('setting', function(){return View('admin.setting');});
    Route::get('materials', function(){return View('admin.materials');});
    Route::get('media/{id}', function(){return View('admin.media');});

        Route::group(['prefix' => 'help'], function (){
            Route::get('navigation', function(){return View('help.main');});
            Route::get('test', function(){return View('help.test');});
            Route::get('types', function(){return View('help.testtypes');});
            Route::get('notest', function(){return View('help.notest');});
            Route::get('results', function(){return View('help.results');});
        });
});



/*----------------------DEBUG ROUTES-----------------------------------*/

Route::get('/testSession', function (\Illuminate\Http\Request $request){
    $id = $request->session()->get('sessionId');
    $session = \TestEngine\TestSessionHandler::getSession($id);
    dd($session);
});

Route::get('/testRepo', function (\Illuminate\Http\Request $request){
    $repo =  app()->make(\Repositories\TestResultRepository::class);
    $result =  $repo->getLastForUser(5,5);
    dd($result);
});

Route::get('/deleteFile', function (\Illuminate\Http\Request $request){
   \Helpers\FileHelper::delete('images/questions/t81cnrp5nbebrk6zflv9kan9obeg1v.png');
});

Route::get('/getGroupSemester', function (\Illuminate\Http\Request $request){
    $manager =  app()->make(\Managers\GroupManager::class);
    $groupId = $request->query('groupId');
    $result =  $manager->getCurrentSemesterForGroup($groupId);
    dd($result);
});

Route::get('/redisTest', function (){
    $tracker = app()->make(TestSessionTracker::class);
    dd($tracker->getCurrentSessions());
});



/*---------------------------------------------------------------------*/




Route::group(['prefix' => 'api'], function() {

    /*------------------------------------------------------------------------------
     *                       ПОЛЬЗОВАТЕЛИ
     * -----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'user'], function () {

        Route::get('role', 'UserController@getRoleByUser');
        Route::post('setCurrentUserPassword', 'UserController@setCurrentUserPassword');
        Route::get('show', 'UserController@getByNameAndGroupPaginated');
        Route::get('current', 'UserController@getCurrentUserInfo');
        Route::post('setPassword', 'UserController@setUserPassword')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('delete/{id}', 'UserController@deleteUser')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('activate/{id}', 'UserController@activate')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::get('getStudent/{id}', 'UserController@getStudentInfo');
    });

    /*--------------------------------------------------------------------------------
     *      Организационная структура ВУЗа (Институты, профили)
     * -------------------------------------------------------------------------------
     */
    Route::get('institute/{id}/profiles', 'OrgStructureController@getInstituteProfiles');
    Route::get('institutes', 'OrgStructureController@getAllInstitutes');
    Route::get('profiles', 'OrgStructureController@getAllProfiles');


    Route::group(['prefix' => 'profile',
        'middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
        function () {
        Route::get('{id}/groups', 'OrgStructureController@getProfileGroups');
        Route::get('{id}/plans', 'OrgStructureController@getProfilePlans');
        Route::get('{id}/disciplines', 'OrgStructureController@getProfileDisciplines');

        Route::post('create', 'OrgStructureController@createProfile');
        Route::post('update', 'OrgStructureController@updateProfile');
        Route::post('delete/{id}', 'OrgStructureController@deleteProfile');
    });


    /*------------------------------------------------------------------------------
     *                       УЧЕБНЫЕ ПЛАНЫ
     * -----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'plan',
        'middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
        function () {
            Route::get('{id}', 'StudyPlanController@getPlan');
            Route::get('profile/{id}', 'StudyPlanController@getPlansByProfile');
            Route::get('{id}/disciplines', 'StudyPlanController@getPlanDisciplines');
            Route::post('create', 'StudyPlanController@create');
            Route::post('update', 'StudyPlanController@update');
            Route::post('delete/{id}', 'StudyPlanController@delete');

            /*------------------------------------------------------------------------
            *                      Работа с дисциплинами планов                      */

            Route::group(['prefix' => 'discipline'], function () {
                Route::get('{id}/marks', 'StudyPlanController@getDisciplinePlanMarkTypes');
                Route::post('show', 'StudyPlanController@getPlansDisciplinesByStudyplanAndNamePaginated');
                Route::post('create', 'StudyPlanController@addDisciplinePlan');
                Route::post('update', 'StudyPlanController@updateDisciplinePlan');
                Route::post('delete/{id}', 'StudyPlanController@deleteDisciplinePlan');
            });

            /*------------------------------------------------------------------------
            *                      Работа с оценками по дисциплинам планов           */

            Route::group(['prefix' => 'mark'], function () {
                Route::post('create', 'StudyPlanController@addDisciplinePlan');
                Route::post('update', 'StudyPlanController@updateDisciplinePlan');
                Route::post('delete/{id}', 'StudyPlanController@deleteDisciplinePlan');
                Route::post('linkTest', 'StudyPlanController@linkMarkToTest');
        });
    });

    /*-----------------------------------------------------------------------------
     *                           ГРУППЫ
     *-----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'groups',
        'middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
        function () {
            Route::get('show', 'GroupController@getProfileGroupsByNamePaginated');
            Route::get('{id}', 'GroupController@getGroup');
            Route::post('create', 'GroupController@create');
            Route::post('update', 'GroupController@update');
            Route::post('delete/{id}', 'GroupController@delete');
            Route::get('{id}/students', 'GroupController@getGroupStudents');
            Route::get('{id}/studyplan', 'GroupController@getGroupStudyPlan');
            Route::get('{id}/hasUnactive', 'GroupController@hasUnactive');
            Route::post('acceptAll/{id}', 'GroupController@acceptAll');

            /*------------------------------------------------------------------------
            *                      Работа со студентами группы                       */

            Route::group(['prefix' => 'student'], function () {
                Route::post('create', 'GroupController@createStudent');
                Route::post('update', 'GroupController@updateStudent');
                Route::post('delete/{id}', 'GroupController@deleteStudent');
                Route::post('setGroup', 'GroupController@setStudentGroup');
        });
    });

    Route::get('groups/', 'GroupController@getAll');


    /*-----------------------------------------------------------------------------
     *                           ДИСЦИПЛИНЫ
     *-----------------------------------------------------------------------------
     */
    Route::group(['prefix' => 'disciplines'], function () {
        Route::get('/', 'DisciplineController@getAll');
        Route::post('create', 'DisciplineController@create')
            ->middleware('checkRole:'.UserRole::Admin);
        Route::post('update', 'DisciplineController@update')
            ->middleware('checkRole:'.UserRole::Admin);
        Route::post('delete/{id}', 'DisciplineController@delete')
            ->middleware('checkRole:'.UserRole::Admin);
        Route::get('show', 'DisciplineController@getByNameAndProfilePaginated');
        Route::get('actual', 'DisciplineController@getActualDisciplinesForStudent');
        Route::get('testresults', 'DisciplineController@getDisciplinesWhereTestsPassed');
        Route::get('name/{name}','DisciplineController@getByName');
        Route::get('{id}/profiles', 'DisciplineController@getDisciplineProfilesIds');
        Route::get('{id}/tests', 'DisciplineController@getTestsByDiscipline');
        Route::get('{id}', 'DisciplineController@getDiscipline');

        /*------------------------------------------------------------------------
        *                   Работа со темами дисциплин                          */

        Route::get('{id}/themes', 'DisciplineController@getThemes');

        Route::group(['prefix' => 'themes',
            'middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
            function () {
            Route::post('create', 'DisciplineController@createTheme');
            Route::post('update', 'DisciplineController@updateTheme');
            Route::post('delete/{id}', 'DisciplineController@deleteTheme');
            Route::get('{id}', 'DisciplineController@getTheme');
        });
    });


    /*-----------------------------------------------------------------------------
     *                           ПРЕПОДАВАТЕЛИ
     *-----------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'lecturers',
        'middleware' => 'checkRole:'.UserRole::Admin], function () {
        Route::post('create', 'LecturerController@create');
        Route::post('update', 'LecturerController@update');
        Route::post('delete/{id}', 'LecturerController@delete');
        Route::get('show', 'LecturerController@getByNamePaginated');
    });


    /*-----------------------------------------------------------------------------
    *                             МЕДИЯ
    *-----------------------------------------------------------------------------
   */
    Route::group(['prefix' => 'media'], function () {
        Route::post('create', 'MediaController@create');
        Route::post('update', 'MediaController@update');
        Route::post('delete/{id}', 'MediaController@delete');
        Route::post('deletefile', 'MediaController@deleteFile');
        Route::get('hash/{id}', 'MediaController@getMediaByHash');
        Route::get('{id}', 'MediaController@getMedia');
        Route::post('createdocx', 'MediaController@createDocx');
        Route::get('type/{type}', 'MediaController@getMediaByType');
    });

    /*-----------------------------------------------------------------------------
   *                             СВЯЗИ МЕДИИ
   *-----------------------------------------------------------------------------
  */
    Route::group(['prefix' => 'mediable'], function () {
        Route::post('create', 'MediableController@create');
        Route::post('update', 'MediableController@update');
        Route::post('delete/{id}', 'MediableController@delete');
        Route::get('media/{id}', 'MediableController@getAllByMedia');
        Route::get('discipline/{id}', 'MediableController@getAllByDiscipline');
        Route::get('theme/{id}', 'MediableController@getAllByTheme');
        Route::get('themeMedia', 'MediableController@getAllByThemeAndMedia');
        Route::get('disciplineMedia', 'MediableController@getAllByDisciplineAndMedia');
        Route::get('{id}', 'MediableController@getMediable');
    });



    /*-----------------------------------------------------------------------------
     *                              ВОПРОСЫ
     *-----------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'questions',
        'middleware' => 'checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer],
        function () {
        Route::post('create', 'QuestionController@create');
        Route::post('update', 'QuestionController@update');
        Route::post('delete/{id}', 'QuestionController@delete');
        Route::get('show', 'QuestionController@getByThemeAndTextPaginated');
        Route::get('{id}', 'QuestionController@get');
    });

    /*-----------------------------------------------------------------------------
    *                              ТЕСТЫ
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'tests'], function () {
        Route::post('create', 'TestController@create')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('update', 'TestController@update')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('delete/{id}', 'TestController@delete')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::get('show', 'TestController@getByNameAndDisciplinePaginated');
        Route::get('{id}/themes', 'TestController@getThemesOfTest');

        Route::get('showForStudent', 'TestController@getStudentTestsByDiscipline');
        Route::post('start', 'TestProcessController@startTest');
        Route::post('answer', 'TestProcessController@answer');
        Route::get('nextQuestion', 'TestProcessController@getNextQuestion');

        Route::get('sessions', 'TestTrackingController@showSessions')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
    });

    /*-----------------------------------------------------------------------------
    *                        ДОПОЛНИТЕЛЬНЫЕ ПОПЫТКИ
    *------------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'attempts'], function () {

        Route::get('get', 'TestResultController@getExtraAttemptsCount');
        Route::post('set', 'TestResultController@setExtraAttempts')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
    });
    /*-----------------------------------------------------------------------------
    *                           РЕЗУЛЬТАТЫ ТЕСТОВ
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'results'], function () {
        Route::get('show', 'TestResultController@getResults')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::get('getGroupResults','TestResultController@getGroupResults');
        // ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('setMark', 'TestResultController@setAnswerMark')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::get('/getByUserAndTest', 'TestResultController@getByUserAndTest')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
        Route::post('deleteOld', 'TestResultController@deleteOldResults')
            ->middleware('checkRole:'.UserRole::Admin);
        Route::get('/discipline/{id}', 'TestResultController@getByDiscipline');
        Route::get('/show/{id}', 'TestResultController@getByIdForStudent');
        Route::get('{id}', 'TestResultController@getById');
    });

    /*-----------------------------------------------------------------------------
   *                                ПРОГРАММЫ
   *------------------------------------------------------------------------------
   */
    Route::group(['prefix' => 'program'], function () {
        Route::post('run', 'ProgramController@run');
        Route::get('byQuestion/{id}','ProgramController@getByQuestion');
    });

    /*-----------------------------------------------------------------------------
    *                     НАСТРОЙКИ СИСТЕМЫ ТЕСТИРОВАНИЯ
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'settings'], function () {
        Route::get('get/{key}', 'SettingsController@get');
        Route::get('getDefaults', 'SettingsController@getDefaults');
        Route::get('getAll', 'SettingsController@getAll');
        Route::post('set', 'SettingsController@set')
            ->middleware('checkRole:'.UserRole::Admin);
    });

    /*-----------------------------------------------------------------------------
    *                             НАСТРОЙКИ ДЛЯ UI
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'uisettings'], function () {
        Route::post('get', 'UISettingsController@getSettings');
        Route::post('set', 'UISettingsController@setSettings');
    });

    /*-----------------------------------------------------------------------------
    *                             ИМПОРТ/ЭКСПОРТ
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'import'], function () {
        Route::post('questions', 'ImportExportController@importQuestions')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);

        Route::get('all', 'ImportExportController@importAll')
            ->middleware('checkRole:'.UserRole::Admin);
    });
    Route::group(['prefix' => 'export'], function () {
        Route::get('questions/{themeId}', 'ImportExportController@exportQuestions')
            ->middleware('checkRole:'.UserRole::Admin.'|'.UserRole::Lecturer);
    });

     /*-----------------------------------------------------------------------------
    *                          ВНЕШНЕЕ API ДЛЯ МОДУЛЯ ПРОГРАММНОГО КОДА
    *------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'external'], function () {
        Route::post('setMark', 'TestResultController@setAnswerMark')
            ->middleware('checkIP');
        Route::post('createGivenAnswer', 'TestResultController@createGivenAnswer')
            ->middleware('checkIP');
    });


});