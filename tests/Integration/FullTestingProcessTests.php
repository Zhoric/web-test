<?php
use Helpers\DateHelper;
use Illuminate\Support\Facades\DB;

require realpath(__DIR__ . '/..')."/TestDataSource.php";

/**
 * Тестирование полного процесса создания и прохождения теста студентом.
 * Запуск:
 *          cd /var/www/web-test
 *          phpunit --debug
 *
 *     Рассматривается тривиальный процесс прохождения студентом единственного теста по единственной теме единственной дисциплины с пятью вопросами.
 * Также рассматривается создание всех необходимых сущностей (институт, профиль, учебный план...).
 * Создание института, профиля, учётной записи администратора и ролей не является обязанностью разработанной системы,
 * поэтому происходит при прямом взаимодействии с базой данных. Взаимодействие и создание остальных сущностей происходит
 * посредством WEB API системы с использованием запросов с данными в формате JSON.
 *
 *     Настройки среды, которые могут повлиять на успешность теста:
 * -- серверное время - от времени зависит определение текущего семестра для студента, а следовательно список дисциплин, по которым студент может пройти тест в текущий момент.
 * -- работа Redis Cache - для корректной работы процесса тестирования обязателен запуск сервера Redis Cache.
 * -- наличие базы данных, указанной в файле .env с применением всех миграций.
 *
 */
class FullTestingProcessTests extends TestCase
{
    // Данные тестовых учётных записей
    private static $ADMIN_EMAIL = 'admin@admin.ru';
    private static $LECTURER_EMAIL = 'lecturer@lecturer.ru';
    private static $STUDENT_EMAIL = 'student@student.ru';
    private static $PASSWORD = '123456';

    // Идентификаторы тестовых учётных записей
    private static $ADMIN_ID = 1;
    private static $LECTURER_ID = 2;
    private static $STUDENT_ID = 3;

    // Роли тестовых учётных записей
    private static $ADMIN_ROLE_ID = 1;
    private static $LECTURER_ROLE_ID = 2;
    private static $STUDENT_ROLE_ID = 3;

    // Идентификаторы тестовых сущностей
    private static $INSTITUTE_ID = 1;
    private static $ISIT_PROFILE_ID = 1;
    private static $ISIT_STUDY_PLAN_ID = 1;
    private static $WEB_DISCIPLINE_ID = 1;
    private static $HTML_DISCIPLINE_THEME_ID = 1;
    private static $STUDENT_GROUP_ID = 1;
    private static $WEB_TEST_ID = 1;
    private static $TEST_RESULT_ID = 1;

    // Идентификаторы тестовых вопросов по дисциплине "WEB-технологии"
    private static $HTML_MARKUP_QUESTION_ID = 1;
    private static $CSS_QUESTION_ID = 2;
    private static $JAVASCRIPT_QUESTION_ID = 3;
    private static $HTML_VERSION_QUESTION_ID = 4;
    private static $MIME_TYPES_QUESTION_ID = 5;

    // Пути к папкам относительно папки /public
    private static $QUESTION_IMAGES_DIR = "/images/questions";
    private static $IMPORT_EXPORT_DIR = "/import";

    //Текст сообщений об ошибках
    private static $AUTHORISATION_REPEAT_ERROR = 'Вы уже вошли под другим пользователем!';
    private static $USER_NOT_EXISTS_ERROR = 'Такого пользователя не существует!';
    private static $ACCOUNT_NOT_CONFIRMED = 'Ваш аккаунт ещё не подтвержден администратором!';
    private static $NO_ATTEMPTS_FOR_TEST_LEFT = 'Все попытки прохождения теста исчерпаны!';

    //Идентификатор сессии тестирования студента (заполняется при запуске теста студентом).
    private static $TEST_SESSION_ID;

    //Идентификатор ответа на открытый вопрос (должен проверяться вручную преподавателем).
    private static $OPEN_QUESTION_ANSWER_ID;

    //Константы процесса тестирования.
    private static $QUESTIONS_COUNT = 5;
    private static $EXPECTED_RESULT_MARK = 75;

    /**
     * Тестирование полного процесса создания и прохождения теста студентом.
     */
    public function testFullProcess(){
        $this->writeConsoleMessage(PHP_EOL.'---- ЗАПУСК ТЕСТИРОВАНИЯ ПОЛНОГО ПРОЦЕССА. -----', 'cyan', 2);

        $this->createInstitute();
        $this->createProfile();
        $this->createRoles();
        $this->createAdminAccount();
        $this->checkWrongCredentialsAuthorization('fakemail@mail.ru', 'qwerty');

        $this->logIn(self::$ADMIN_EMAIL, self::$PASSWORD);
        $this->checkRepeatableLoginDisabled(self::$ADMIN_EMAIL, self::$PASSWORD);
        $this->createDiscipline();
        $this->createStudyPlan();
        $this->addDisciplineToStudyPlan();
        $this->createLecturerWithDisciplineAttached();
        $this->logOut();

        $this->logIn(self::$LECTURER_EMAIL, self::$PASSWORD);
        $this->createGroupWithStudyPlanAttached();
        $this->createDisciplineTheme();
        $this->addClosedOneAnswerQuestionWithImage();
        $this->addClosedManyAnswersQuestion();
        $this->addClosedOneAnswerQuestion();
        $this->addOpenSingleStringQuestion();
        $this->addOpenManyStringsQuestion();
        $this->createTest();
        $this->logOut();

        $this->sendStudentRegistrationApplication();
        $this->checkLoginWithoutAcceptionUnavailable();

        $this->logIn(self::$LECTURER_EMAIL, self::$PASSWORD);
        $this->acceptStudentRegistrationApplication();
        $this->logOut();

        $this->logIn(self::$STUDENT_EMAIL, self::$PASSWORD);
        $this->checkIsDisciplineAvailableForStudent();
        $this->checkIfCreatedTestAvailableForStudent();
        $this->startTest();
        $this->checkRedisTestSessionCreated();
        $this->answerAllTestQuestions();
        $this->checkTestResultHasNoMark();
        $this->checkTestResultAvailableInResultsList();
        $this->checkStartTestUnavailableIfNoAttemptsLeft();
        $this->logOut();

        $this->logIn(self::$LECTURER_EMAIL, self::$PASSWORD);
        $this->checkTestResultAvailableForLecturer();
        $this->checkSetOpenQuestionMark();
        $this->checkResultMarkReCalculated();
        $this->checkAddExtraAttempt();
        $this->logOut();

        $this->logIn(self::$STUDENT_EMAIL, self::$PASSWORD);
        $this->checkStudentCanSeeNewMark();
        $this->checkStudentCanStartTestIfExtraAttemptWasAdded();
        $this->logOut();

        $this->writeConsoleMessage(PHP_EOL.'---- ТЕСТИРОВАНИЕ ПОЛНОГО ПРОЦЕССА ЗАВЕРШЕНО УСПЕШНО. ----', 'green', 2);
    }

    /**
     * Действия, которые будут выполнены перед запуском теста.
     */
    protected function setUp()
    {
        $this->askForConfirmation();
        parent::setUp();
        $this->createApplication();
        $this->clearTables();
        $this->checkRedisServerIsRunning();
        $this->clearRedis();
        $this->writeConsoleMessage('Заполнение данными таблицы глобальных настроек тестирования. [Seed]', 'grey', 1);
        $this->seed('SettingsTableSeeder');
        $this->checkQuestionImagesDirectoryWritable();
        $this->checkImportDirectoryWritable();
        $this->clearPublicFolder(self::$QUESTION_IMAGES_DIR);
    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->clearTables();
        $this->clearRedis();
        $this->clearPublicFolder(self::$QUESTION_IMAGES_DIR);
    }

    /**
     * Очистка всех задействованных в тесте таблиц.
     */
    private function clearTables(){
        $this->writeNewLine();
        $this->writeConsoleMessage('Очистка таблиц. [Query]', 'grey', 1);

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        DB::table('discipline')->truncate();
        DB::table('test')->truncate();
        DB::table('discipline_plan')->truncate();
        DB::table('theme')->truncate();
        DB::table('question')->truncate();
        DB::table('answer')->truncate();
        DB::table('given_answer')->truncate();
        DB::table('student_group')->truncate();
        DB::table('test_settings')->truncate();
        DB::table('test_result')->truncate();
        DB::table('discipline_lecturer')->truncate();
        DB::table('studyplan')->truncate();
        DB::table('profile')->truncate();
        DB::table('institute')->truncate();
        DB::table('user')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_user')->truncate();
        DB::table('extra_attempt')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }


    /**
     * Удаление всех файлов из указанной директории относительно директории /public.
     * @param $path - Расположение папки относительно директории /public.
     */
    private function clearPublicFolder($path){
        $fullPath = public_path().$path;
        $this->writeConsoleMessage("Очистка директории $fullPath. [DIR]", 'grey', 1);

        array_map('unlink', glob("$fullPath/*"));
    }

    /**
     * Проверка доступа к директории для хранения изображений вопросов.
     */
    private function checkQuestionImagesDirectoryWritable(){
        $fullPath = public_path().self::$QUESTION_IMAGES_DIR;
        $this->checkDirectoryAccess($fullPath);
    }

    /**
     * Проверка доступа к директории для временных файлов импорта/экспорта вопросов.
     */
    private function checkImportDirectoryWritable(){
        $fullPath = public_path().self::$IMPORT_EXPORT_DIR;
        $this->checkDirectoryAccess($fullPath);
    }

    /**
     * Проверка доступа к директории.
     * @param $fullPath - полный путь к директории.
     */
    private function checkDirectoryAccess($fullPath){
        $this->writeConsoleMessage('Проверка доступа к директории '.$fullPath.' [DIR]', 'grey', 1);

        $this->assertFileExists($fullPath);
        $this->assertFileIsReadable($fullPath);
        $this->assertFileIsWritable($fullPath);
    }

    /**
     * Проверка работы сервера Redis Cache.
     */
    private function checkRedisServerIsRunning(){
        $this->writeConsoleMessage('Проверка подключения к серверу Redis Cache [Redis]', 'grey', 1);
        $serverResponse = $this->getRedisClient()->connection()->ping();
        $this->assertEquals('PONG', $serverResponse);
    }

    /**
     * Очистка хранилища Redis Cache.
     */
    private function clearRedis(){
        $this->writeConsoleMessage('Очистка хранилища Redis Cache. [Redis]', 'grey', 1);
        $this->getRedisClient()->connection()->flushall();
    }

    /**
     * Добавление ролей пользователей в БД.
     */
    private function createRoles(){
        $this->writeConsoleMessage('Добавление ролей пользователей в БД. [Query]');

        DB::table('roles')->insert(array(
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Администратор'));

        DB::table('roles')->insert(array(
            'name' => 'Lecturer',
            'slug' => 'lecturer',
            'description' => 'Преподаватель'));

        DB::table('roles')->insert(array(
            'name' => 'Student',
            'slug' => 'student',
            'description' => 'Студент'));

        $this->seeInDatabase('roles', ['name' => 'Admin', 'slug' => 'admin']);
        $this->writeOk();
    }

    /**
     * Добавление данных об институте в БД.
     */
    private function createInstitute(){
        $this->writeConsoleMessage('Добавление института в БД. [Query]');

        DB::table('institute')->insert(array(
            'id' => self::$INSTITUTE_ID,
            'name' => 'ИНСТИТУТ ИНФОРМАЦИОННЫХ ТЕХНОЛОГИЙ И УПРАВЛЕНИЯ В ТЕХНИЧЕСКИХ СИСТЕМАХ',
            'description' => 'Описание.'));

        $this->seeInDatabase('institute', ['name' =>
            'ИНСТИТУТ ИНФОРМАЦИОННЫХ ТЕХНОЛОГИЙ И УПРАВЛЕНИЯ В ТЕХНИЧЕСКИХ СИСТЕМАХ', 'id' => self::$INSTITUTE_ID]);
        $this->writeOk();
    }

    /**
     * Создание учётной записи администратора в БД.
     */
    private function createAdminAccount(){
        $this->writeConsoleMessage('Добавление учётной записи администратора в БД. [Query]');

        DB::table('user')->insert(array(
            'id' => self::$ADMIN_ID,
            'firstname' => '',
            'lastname' => 'Администратор',
            'email' => self::$ADMIN_EMAIL,
            'password' => bcrypt(self::$PASSWORD),
            'active' => true));

        $this->seeInDatabase('user', ['id' => self::$ADMIN_ID, 'email' => self::$ADMIN_EMAIL]);

        DB::table('role_user')->insert([
            'role_id' => self::$ADMIN_ID,
            'user_id' => self::$ADMIN_ROLE_ID,
        ]);

        $this->seeInDatabase('role_user', ['role_id' => self::$ADMIN_ID, 'user_id' => self::$ADMIN_ROLE_ID]);
        $this->writeOk();
    }

    /**
     * Авторизация в системе.
     * @param $email - Почтовый адрес пользователя.
     * @param $password - Пароль пользователя.
     */
    private function logIn($email, $password){
        $this->writeNewLine();
        $this->writeConsoleMessage('Авторизация с email: '.$email.' и паролем: '.$password.'. [API]', 'blue');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST',$apiUri , ['email' => $email, 'password' => $password])
            ->seeJson(['Success' => true]);
        $this->writeOk();
    }

    private function logOut(){
        $this->writeConsoleMessage('Выход из учётной записи. [API]', 'blue');
        $apiUri = '/logout';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri)
            ->followRedirects()
            ->see('Войти');
        $this->writeOk(1);
    }

    /**
     * Проверка невозможности повторной авторизации при наличии авторизованного пользователя.
     * @param $email - Почтовый адрес пользователя.
     * @param $password - Пароль пользователя.
     */
    private function checkRepeatableLoginDisabled($email, $password){
        $this->writeConsoleMessage('Проверка отсутствия возможности повторной авторизации. [API]');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['email' => $email, 'password' => $password])
            ->seeJson(['Success' => false, 'Message' => self::$AUTHORISATION_REPEAT_ERROR]);
        $this->writeOk();
    }

    private function checkWrongCredentialsAuthorization($email, $password){
        $this->writeConsoleMessage('Проверка отсутствия возможности авторизации с данными не существующего пользователя. [API]');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['email' => $email, 'password' => $password])
            ->seeJson(['Success' => false, 'Message' => self::$USER_NOT_EXISTS_ERROR]);
        $this->writeOk();
    }

    /**
     * Добавление в БД данных о направлении обучения студентов "ИСиТ".
     */
    private function createProfile(){
        $this->writeConsoleMessage('Добавление направления подготовки студентов в БД. [Query]');

        DB::table('profile')->insert(array(
            'institute_id' => self::$INSTITUTE_ID,
            'code' => '09.03.02',
            'name' => 'ИСиТ',
            'fullname' =>'Информационные системы и технологии',
            'semesters' => 8));

        $this->seeInDatabase('profile', ['id' => self::$ISIT_PROFILE_ID, 'name' => 'ИСиТ']);
        $this->writeOk();
    }

    /**
     * Создание учебного плана для профиля подготовки ИСиТ через API.
     */
    private function createStudyPlan(){
        $this->writeConsoleMessage('Создание учебного плана. [API]');
        $apiUri = '/api/plan/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['studyPlan' =>
            ["name" => "Учебный план ИСиТ 2017 бакалавр очная форма"], "profileId" => self::$ISIT_PROFILE_ID])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('studyplan', ['name' => "Учебный план ИСиТ 2017 бакалавр очная форма",
            "id" => self::$ISIT_STUDY_PLAN_ID]);
        $this->writeOk();
    }

    /**
     * Добавление дисциплины "WEB-технологии" через API.
     */
    private function createDiscipline(){
        $this->writeConsoleMessage('Добавление дисциплины. [API]');
        $apiUri = '/api/disciplines/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['discipline' =>
            ["name" => "WEB-Технологии",
            "abbreviation" => "WEB"],
            "profileIds" => [self::$ISIT_PROFILE_ID]])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('discipline', ['abbreviation' => "WEB", 'id' => self::$WEB_DISCIPLINE_ID]);
        $this->writeOk();
    }

    /**
     * Добавление дисциплины в учебный план ИСиТ через API.
     */
    private function addDisciplineToStudyPlan(){
        $this->writeConsoleMessage('Добавление дисциплины в учебный план. [API]');
        $apiUri = 'api/plan/discipline/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['disciplinePlan' =>
            ["startSemester" => 1,
                "semestersCount" => 3,
                "hours" => 250,
                "hasProject" => true,
                 "hasExam" => true],
            "studyPlanId" => self::$ISIT_STUDY_PLAN_ID,
            "disciplineId" => self::$WEB_DISCIPLINE_ID])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('discipline_plan', [
            'discipline_id' => self::$WEB_DISCIPLINE_ID,
            'studyplan_id' => self::$ISIT_STUDY_PLAN_ID,
            'hours' => 250,
            "start_semester" => 1]);
        $this->writeOk();
    }

    /**
     * Создание учётной записи преподавателя администратором с назначением дисциплины (WEB-технологии).
     */
    private function createLecturerWithDisciplineAttached(){
        $this->writeConsoleMessage('Создание учётной записи преподавателя c закреплением за ним дисциплины. [API]');
        $apiUri = '/api/lecturers/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ["lecturer" =>[
            'firstName' => 'Александр',
            'lastName' => 'Овчинников',
            'email' => self::$LECTURER_EMAIL,
            'password' => self::$PASSWORD
        ], "disciplineIds" => [self::$WEB_DISCIPLINE_ID]])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('user',['email' => self::$LECTURER_EMAIL]);
        $this->seeInDatabase('role_user', ['user_id' => self::$LECTURER_ID,
            'role_id' => self::$LECTURER_ROLE_ID]);
        $this->seeInDatabase('discipline_lecturer', ['lecturer_id' => self::$LECTURER_ID,
            'discipline_id' => self::$WEB_DISCIPLINE_ID]);
        $this->writeOk();
    }

    /**
     * Создание группы ИСб-41о и закрепление за ней учебного созданного ранее учебного плана.
     */
    private function createGroupWithStudyPlanAttached(){
        $this->writeConsoleMessage('Создание группы с закреплением за ней учебного плана. [API]');
        $apiUri = 'api/groups/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ["group" => [
            'id' => self::$STUDENT_GROUP_ID,
            'prefix' => 'ИС',
            'course' => 4,
            'number' => 1,
            'isFulltime' => true,
            'name' => 'ИСб-41о'
        ], "studyPlanId" => self::$ISIT_STUDY_PLAN_ID])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('group', ['id' => self::$STUDENT_GROUP_ID,
            'name' => 'ИСб-41о',
            'studyplan_id' => self::$ISIT_STUDY_PLAN_ID]);
        $this->writeOk();
    }

    /**
     * Создание темы дисциплины WEB.
     */
    private function createDisciplineTheme(){
        $this->writeConsoleMessage('Добавление темы дисциплины. [API]');
        $apiUri = '/api/disciplines/themes/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ["theme" =>[
            'id' => self::$HTML_DISCIPLINE_THEME_ID,
            'name' => 'Язык гиперекстовой разметки HTML',
        ], "disciplineId" => self::$WEB_DISCIPLINE_ID])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('theme', ['id' => self::$HTML_DISCIPLINE_THEME_ID,
            'name' => 'Язык гиперекстовой разметки HTML',
            'discipline_id' => self::$WEB_DISCIPLINE_ID]);
        $this->writeOk();
    }

    /**
     * Создание закрытого вопроса теста с единственным правильным ответом и изображением.
     */
    private function addClosedOneAnswerQuestionWithImage(){
        $this->writeConsoleMessage('Создание закрытого вопроса теста с единственным правильным ответом и изображением. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$HTML_MARKUP_QUESTION_ID,
                'type' => QuestionType::ClosedOneAnswer,
                'text' => 'Что означает вторая буква в названии языка HTML?',
                'complexity' => QuestionComplexity::Low,
                'time' => 20],
            "theme" => self::$HTML_DISCIPLINE_THEME_ID,
            "answers" =>
                [['text' => 'Makeup', 'isRight' => false],
                ['text' => 'Markup', 'isRight' => true],
                ['text' => 'Malware', 'isRight' => false]],
            //"file" => TestDataSource::getTestImageBase64Content(),
            //"fileType" => TestDataSource::getTestImageFileType(),
        ])
            ->seeJson(['Success' => true]);

        /*
        $this->seeInDatabase('question', [
            'theme_id' => self::$HTML_DISCIPLINE_THEME_ID,
            'type' => QuestionType::ClosedOneAnswer,
            'time' => 20])
            ->notSeeInDatabase('question', [                // У вопроса в БД должна быть ссылка на изображение
                'id' => self::$HTML_MARKUP_QUESTION_ID,
                'image' => null
            ]);

        $this->seeInDatabase('answer', ['text' => 'Makeup', 'is_right' => false,
            'question_id' => self::$HTML_MARKUP_QUESTION_ID])
            ->seeInDatabase('answer', ['text' =>'Markup', 'is_right' => true,
                'question_id' => self::$HTML_MARKUP_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'Malware', 'is_right' => false,
                'question_id' => self::$HTML_MARKUP_QUESTION_ID]);
        $this->writeOk();

        $questionImagesFolderPath = public_path().self::$QUESTION_IMAGES_DIR;
        $this->writeConsoleMessage("Проверка наличия файла изображения в папке $questionImagesFolderPath. [DIR]");
        $this->assertEquals(1, (count(glob($questionImagesFolderPath."/*"))));
        $this->writeOk();
        */
    }

    /**
     * Создание закрытого вопроса теста с несколькими правильными ответами.
     */
    private function addClosedManyAnswersQuestion(){
        $this->writeConsoleMessage('Создание закрытого вопроса теста с несколькими правильными ответами. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$CSS_QUESTION_ID,
                'type' => QuestionType::ClosedManyAnswers,
                'text' => 'Укажите свойства CSS, отвечающие за отступы HTML-элемента.',
                'complexity' => QuestionComplexity::Low,
                'time' => 45],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "answers" =>
                    [['text' => 'margin', 'isRight' => true],
                        ['text' => 'background-color', 'isRight' => false],
                        ['text' => 'font-weight', 'isRight' => false],
                        ['text' => 'padding', 'isRight' => true]]
            ])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('question', [
            'type' => QuestionType::ClosedManyAnswers,
            'time' => 45]);

        $this->seeInDatabase('answer', ['text' => 'margin', 'is_right' => true,
            'question_id' => self::$CSS_QUESTION_ID])
            ->seeInDatabase('answer', ['text' =>'background-color', 'is_right' => false,
                'question_id' => self::$CSS_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'font-weight', 'is_right' => false,
                'question_id' => self::$CSS_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'padding', 'is_right' => true,
                'question_id' => self::$CSS_QUESTION_ID]);
        $this->writeOk();
    }

    /**
     * Создание закрытого вопроса теста с единственным правильным ответом.
     */
    private function addClosedOneAnswerQuestion(){
        $this->writeConsoleMessage('Создание закрытого вопроса теста с единственным правильным ответом. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$JAVASCRIPT_QUESTION_ID,
                'type' => QuestionType::ClosedOneAnswer,
                'text' => 'При помощи какой из перечисленных операции языка JavaScript можно привести строку к числу?',
                'complexity' => QuestionComplexity::Medium,
                'time' => 30],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "answers" =>
                    [['text' => 'Бинарный +', 'isRight' => false],
                        ['text' => 'Унарный +', 'isRight' => true],
                        ['text' => 'Унарный -', 'isRight' => false]],
            ])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('question', [
            'type' => QuestionType::ClosedOneAnswer,
            'time' => 30]);

        $this->seeInDatabase('answer', ['text' => 'Бинарный +', 'is_right' => false,
            'question_id' => self::$JAVASCRIPT_QUESTION_ID])
            ->seeInDatabase('answer', ['text' =>'Унарный +', 'is_right' => true,
                'question_id' => self::$JAVASCRIPT_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'Унарный -', 'is_right' => false,
                'question_id' => self::$JAVASCRIPT_QUESTION_ID]);
        $this->writeOk();
    }

    /**
     * Создание открытого вопроса теста с однострочным ответом.
     */
    private function addOpenSingleStringQuestion(){
        $this->writeConsoleMessage('Создание открытого вопроса теста с однострочным ответом. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$HTML_VERSION_QUESTION_ID,
                'type' => QuestionType::OpenOneString,
                'text' => 'Какая версия стандарта HTML была одобрена в 1995 году?',
                'complexity' => QuestionComplexity::Medium,
                'time' => 60],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "answers" =>
                    [['text' => '2', 'isRight' => true],
                        ['text' => '2.0', 'isRight' => true],
                        ['text' => 'вторая', 'isRight' => true],
                        ['text' => 'два', 'isRight' => true]],
            ])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('question', [
            'type' => QuestionType::OpenOneString,
            'time' => 60]);

        $this->seeInDatabase('answer', ['text' => '2', 'is_right' => true, 'question_id' => self::$HTML_VERSION_QUESTION_ID])
            ->seeInDatabase('answer', ['text' =>'2.0', 'is_right' => true,'question_id' => self::$HTML_VERSION_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'вторая', 'is_right' => true,'question_id' => self::$HTML_VERSION_QUESTION_ID])
            ->seeInDatabase('answer', ['text' => 'два', 'is_right' => true,'question_id' => self::$HTML_VERSION_QUESTION_ID]);
        $this->writeOk();
    }

    /**
     * Создание открытого вопроса теста с многострочным ответом.
     * [!] Ответ на вопрос данного типа не проверяется автоматически. Проверку должен осуществлять преподаватель.
     */
    private function addOpenManyStringsQuestion(){
        $this->writeConsoleMessage('Создание открытого вопроса теста с многострочным ответом. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$MIME_TYPES_QUESTION_ID,
                'type' => QuestionType::OpenManyStrings,
                'text' => 'Напишите что вам известно о MIME-типах.',
                'complexity' => QuestionComplexity::High,
                'time' => 90],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID
            ])->seeJson(['Success' => true]);

        $this->seeInDatabase('question', [
            'type' => QuestionType::OpenManyStrings,
            'time' => 90]);

        $this->writeOk();
    }

    /*
     * Создание теста по теме "HTML" дисциплины "WEB-технологии".
     */
    private function createTest(){
        $this->writeConsoleMessage('Создание теста по теме дисциплины. [API]');
        $apiUri = '/api/tests/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, [
           'test' => [
               'id' => self::$WEB_TEST_ID,
               'subject' => 'Тест на знание основ HTML, CSS и JavaScript',
               'timeTotal' => 300,
               'attempts' => 1,
               'type' => TestType::Control,
               'isActive' => true
           ],
            'themeIds' => [self::$HTML_DISCIPLINE_THEME_ID],
            'disciplineId' => self::$WEB_DISCIPLINE_ID
        ])->seeJson(['Success' => true]);

        $this->seeInDatabase('test', [
            'id' => self::$WEB_TEST_ID,
            'discipline_id' => self::$WEB_DISCIPLINE_ID,
            'subject' => 'Тест на знание основ HTML, CSS и JavaScript',
            'time_total' => 300,
            'attempts' => 1,
            'type' => TestType::Control,
            'is_active' => true
        ]);

        $this->writeOk();
    }

    /**
     * Отправка студентом заявки на регистрацию в системе.
     */
    private function sendStudentRegistrationApplication(){
        $this->writeConsoleMessage('Отправка студентом заявки на регистрацию в системе. [API]');
        $apiUri = '/register';
        $this->writeApiCall($apiUri);

        $this->json('POST', '/register', [
           'user' => [
               'id' => self::$STUDENT_ID,
               'email' => self::$STUDENT_EMAIL,
               'password' => self::$PASSWORD,
               'firstname' => 'Никита',
               'lastname' => 'Жихарев',
           ],
            'role' => UserRole::Student,
            'groupId' => self::$STUDENT_GROUP_ID
        ])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('user', ['email' => self::$STUDENT_EMAIL, 'active' => false])
            ->seeInDatabase('role_user', ['role_id' => self::$STUDENT_ROLE_ID,
                'user_id' => self::$STUDENT_ID])
            ->seeInDatabase('student_group', ['group_id' => self::$STUDENT_GROUP_ID,
                'student_id' => self::$STUDENT_ID]);

        $this->writeOk();
    }

    /**
     * Проверка невозможности авторизации студента без подтверждения заявки на регистрацию.
     */
    private function checkLoginWithoutAcceptionUnavailable(){
        $this->writeConsoleMessage('Проверка невозможности авторизации без подтверждения заявки студента. [API]');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri , ['email' => self::$STUDENT_EMAIL, 'password' => self::$PASSWORD])
            ->seeJson(['Success' => false, 'Message' => self::$ACCOUNT_NOT_CONFIRMED]);
        $this->writeOk();
    }

    /**
     * Подтверждение преподавателем заявки студента на регистрацию.
     */
    private function acceptStudentRegistrationApplication(){
        $this->writeConsoleMessage('Подтверждение преподавателем заявки студента на регистрацию. [API]');
        $apiUri = '/api/user/activate/'.self::$STUDENT_ID;
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri)
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('user', ['email' => self::$STUDENT_EMAIL, 'active' => true]);

        $this->writeOk();
    }

    /**
     * Проверка доступности дисциплины WEB-технологии для студента.
     */
    private function checkIsDisciplineAvailableForStudent(){
        $this->writeConsoleMessage('Проверка доступности дисциплины из учебного плана для студента. [API]');
        $apiUri = '/api/disciplines/actual';
        $this->writeApiCall($apiUri);

        $this->json('GET', $apiUri)
            ->seeJson(['Success' => true])
            ->seeJsonContains(['id' => self::$WEB_DISCIPLINE_ID]);

        $this->writeOk();
    }

    /**
     * Проверка доступности теста по дисциплине WEB-технологии для студента.
     */
    private function checkIfCreatedTestAvailableForStudent(){
        $this->writeConsoleMessage('Проверка доступности теста по дисциплине WEB-технологии для студента. [API]');
        $apiUri = '/api/tests/showForStudent?discipline='.self::$WEB_DISCIPLINE_ID;
        $this->writeApiCall($apiUri);

        $this->json('GET', $apiUri)
            ->seeJson(['Success' => true])
            ->seeJsonContains(['id' => self::$WEB_TEST_ID, 'attempts' => 1]);

        $this->writeOk();
    }

    /**
     * Запуск процесса тестирования.
     */
    private function startTest(){
        $this->writeConsoleMessage('Запуск процесса тестирования. [API]');
        $apiUri = '/api/tests/start';
        $this->writeApiCall($apiUri);

        $serverResponse = $this->json('POST', $apiUri, [
            'testId' => self::$WEB_TEST_ID,
        ])
            ->seeJson(['Success' => true])
            ->seeInSession('sessionId');
        self::$TEST_SESSION_ID = $serverResponse->decodeResponseJson()['Data']['sessionId'];

        $this->assertNotNull(self::$TEST_SESSION_ID);
        $this->writeOk();
    }

    /**
     * Проверка создания сессии тестирования в Redis Cache при запуске теста.
     */
    private function checkRedisTestSessionCreated(){
        $this->writeConsoleMessage('Проверка создания сессии тестирования в Redis Cache при запуске теста. [Redis]');

        /** @var \TestEngine\TestSessionFactory $testSessionFactory */
        $testSessionFactory = app()->make(\TestEngine\TestSessionFactory::class);
        $testSession = $testSessionFactory->getBySessionId(self::$TEST_SESSION_ID);

        $this->assertNotNull($testSession);
        $this->assertEquals(self::$WEB_TEST_ID, $testSession->getTestId());
        $this->assertEquals(self::$STUDENT_ID, $testSession->getUserId());
        $this->assertEquals(self::$TEST_RESULT_ID, $testSession->getTestResultId());
        $this->writeOk();

        $this->writeConsoleMessage('Проверка наличия всех вопросов в сессии тестирования. [Redis]');
        $this->assertEquals(self::$QUESTIONS_COUNT, count($testSession->getAllQuestionsIds()));
        $this->assertEquals(0, count($testSession->getAnsweredQuestionsIds()));
        $this->writeOk();

        $this->writeConsoleMessage('Проверка корректности времени окончания теста. [Redis]');
        $this->assertGreaterThan(DateHelper::getCurrentUtcDateTimeString(), $testSession->getTestEndDateTime());
        $this->writeOk();
    }

    /**
     * Последовательное получение и отправка ответа на все вопросы теста.
     */
    private function answerAllTestQuestions(){
        $questionsAnswered = 0;

        // Вопросы будут запрашиваться до тех пор, пока с сервера не вернётся результат теста.
        while (true) {
            $questionData = $this->getNextTestQuestion();
            if (!array_key_exists('question', $questionData)) break;

            $answerData = $this->createQuestionAnswerData($questionData);
            $this->answerTestQuestion($answerData);
            $questionsAnswered++;

            // Запоминаем id открытого вопроса, который должен будет проверить преподаватель.
            if ($questionData->question->id == self::$MIME_TYPES_QUESTION_ID)
                self::$OPEN_QUESTION_ANSWER_ID = $questionsAnswered;

        }

        $this->writeConsoleMessage('Проверка общего количества полученных вопросов теста.');
        $this->assertEquals(self::$QUESTIONS_COUNT, $questionsAnswered);
        $this->writeOk();
    }

    /**
     * Получение очередного вопроса студентом.
     */
    private function getNextTestQuestion(){
        $this->writeConsoleMessage('   Получение очередного вопроса студентом. [API]');
        $apiUri = '/api/tests/nextQuestion';
        $this->writeApiCall($apiUri);

        $serverResponse = $this->getJson($apiUri)
            ->seeJson(['Success' => true])
            ->response->content();

        $responseData = json_decode($serverResponse)->Data;
        $this->assertNotNull($responseData);
        $this->writeOk();

        return $responseData;
    }

    /**
     * Создание объекта с данными ответа на вопрос теста.
     * @param $questionData - Данные текущего вопроса.
     * @return array
     * @throws Exception
     */
    private function createQuestionAnswerData($questionData){
        $question = $questionData->question;
        $answers = $questionData->answers;

        $answerData = [
            'questionId' => $question->id,
            'answerIds' => [],
            'answerText' => ''
        ];

        // Заполняем объект ответа на вопрос в соответствии с вопросом.
        switch ($question->id){
            case self::$HTML_MARKUP_QUESTION_ID:{
                //$this->checkQuestionWithImageHasImage($questionData);
                $answerData['answerIds'] = $this->getAnswersIds($answers, ['Markup']);
                break;
            }
            case self::$CSS_QUESTION_ID:{
                $answerData['answerIds'] = $this->getAnswersIds($answers, ['margin', 'padding']);
                break;
            }
            case self::$JAVASCRIPT_QUESTION_ID:{
                $answerData['answerIds'] = $this->getAnswersIds($answers, ['Унарный +']);
                break;
            }
            case self::$HTML_VERSION_QUESTION_ID:{
                $answerData['answerText'] = '2.0';
                break;
            }
            case self::$MIME_TYPES_QUESTION_ID:{
                $answerData['answerText'] = 'Я ничего не знаю о MIME-типах';
                break;
            }
            default: {
                throw new Exception('Для данного вопроса не указаны подходящие ответы');
            }
        }

        return $answerData;
    }

    /**
     * Отправка ответа на вопрос.
     */
    private function answerTestQuestion($answerData){
        $this->writeConsoleMessage('   Отправка ответа на вопрос. [API]');
        $apiUri = '/api/tests/answer';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, $answerData)
            ->seeJson(['Success' => true]);

        $this->writeOk();

        $this->writeConsoleMessage('   Проверка наличия вопроса в списке отвеченных для текущей сессии тестирования. [Redis]');

        /** @var \TestEngine\TestSessionFactory $testSessionFactory */
        $testSessionFactory = app()->make(\TestEngine\TestSessionFactory::class);
        $testSession = $testSessionFactory->getBySessionId(self::$TEST_SESSION_ID);

        $this->assertContains($answerData['questionId'], $testSession->getAnsweredQuestionsIds());
        $this->writeOk();
    }

    /**
     * Проверка доступности изображения для вопроса с изображением.
     */
    private function checkQuestionWithImageHasImage($questionData){
        $this->writeConsoleMessage('   Проверка доступности изображения для вопроса с изображением. [API]');
        $imagePath = $questionData->question->image;
        $this->assertNotNull($imagePath);
        $this->assertFileExists(public_path().$imagePath);
        $this->writeOk();
    }

    /**
     * Проверка отсутствия оценки по результату теста (т.к. среди вопросов есть открытый, проверяющийся преподавателем).
     */
    private function checkTestResultHasNoMark(){
        $this->writeConsoleMessage('Проверка отсутствия оценки по результату теста (среди вопросов есть открытый). [Query]');
        $this->seeInDatabase('test_result',['id' => self::$TEST_RESULT_ID, 'mark' => null]);
        $this->writeOk();
    }

    /**
     * Проверка наличия результата в списке результатов тестирования студента.
     */
    private function checkTestResultAvailableInResultsList(){
        $this->writeConsoleMessage('Проверка наличия результата в списке результатов тестирования студента. [API]');
        $apiUri = '/api/results/discipline/'.self::$WEB_DISCIPLINE_ID;
        $this->writeApiCall($apiUri);

        $this->json('GET', $apiUri)
            ->seeJson(['Success' => true])
            ->seeJsonContains(['testId' => self::$WEB_TEST_ID, 'attempt' => 1]);
        $this->writeOk();
    }

    /**
     * Проверка невозможности запуска тестирования при отсутствии попыток прохождения.
     */
    private function checkStartTestUnavailableIfNoAttemptsLeft(){
        $this->writeConsoleMessage('Проверка невозможности запуска теста без попыток прохождения. [API]');
        $apiUri = '/api/tests/start/';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, [
            'testId' => self::$WEB_TEST_ID,
        ])
            ->seeJson(['Success' => false, 'Message' => self::$NO_ATTEMPTS_FOR_TEST_LEFT]);
        $this->writeOk();
    }

    /**
     * Проверка доступности результата тестирования для преподавателя.
     */
    private function checkTestResultAvailableForLecturer(){
        $this->writeConsoleMessage('Проверка доступности результата тестирования для преподавателя. [API]');
        $apiUri = '/api/results/show?testId='.self::$WEB_TEST_ID.'&groupId='.self::$STUDENT_GROUP_ID;
        $this->writeApiCall($apiUri);

        $this->json('GET', $apiUri)
            ->seeJson(['Success' => true])
            ->seeJsonContains(['testId' => self::$WEB_TEST_ID, 'userId' => self::$STUDENT_ID, 'attempt' => 1]);
        $this->writeOk();
    }

    /**
     * Проверка возможности установки преподавателем оценки за открытый вопрос.
     */
    private function checkSetOpenQuestionMark(){
        $this->writeConsoleMessage('Проверка возможности установки преподавателем оценки за открытый вопрос. [API]');
        $apiUri = '/api/results/setMark';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, [
            'answerId' => self::$OPEN_QUESTION_ANSWER_ID,
            'mark' => 0])
            ->seeJson(['Success' => true]);

        $this->writeOk();
    }

    /**
     * Проверка пересчёта общей оценки студента за тест после оценивания открытого вопроса.
     */
    private function checkResultMarkReCalculated(){
        $this->writeConsoleMessage('Проверка пересчёта оценки за тест после оценивания открытого вопроса. [Query]');

        $this->seeInDatabase('test_result', ['id' => self::$TEST_RESULT_ID, 'mark' => self::$EXPECTED_RESULT_MARK]);
        $this->writeOk();
    }

    /**
     * Проверка возможности добавления студенту дополнительных попыток по тесту.
     */
    private function checkAddExtraAttempt(){
        $this->writeConsoleMessage('Проверка возможности добавления студенту дополнительных попыток по тесту. [API]');
        $apiUri = '/api/attempts/set';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, [
            'userId' => self::$STUDENT_ID,
            'testId' => self::$WEB_TEST_ID,
            'count' => 3])
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('extra_attempt', [
            'user_id' => self::$STUDENT_ID,
            'test_id' => self::$WEB_TEST_ID,
            'count' => 3]);
        $this->writeOk();
    }

    /**
     * Проверка доступности для студента результата тестирования после оценивания открытого вопроса преподавателем.
     */
    private function checkStudentCanSeeNewMark(){
        $this->writeConsoleMessage('Проверка доступности результата для студента после оценки открытого вопроса. [API]');
        $apiUri = '/api/results/discipline/'.self::$WEB_DISCIPLINE_ID;
        $this->writeApiCall($apiUri);

        $this->json('GET', $apiUri)
            ->seeJson(['Success' => true])
            ->seeJsonContains(['testId' => self::$WEB_TEST_ID, 'attempt' => 1, 'mark' => self::$EXPECTED_RESULT_MARK]);
        $this->writeOk();
    }

    /**
     * Проверка возможности повторного прохождения студентом теста после добавления дополнительных попыток.
     */
    private function checkStudentCanStartTestIfExtraAttemptWasAdded(){
        $this->writeConsoleMessage('Проверка возможности запуска теста после добавления дополнительных попыток. [API]');
        $apiUri = '/api/tests/start';
        $this->writeApiCall($apiUri);

        $this->json('POST', '/api/tests/start', [
            'testId' => self::$WEB_TEST_ID,
        ])
            ->seeJson(['Success' => true]);
        $this->writeOk();
    }

    /**
     * Выбор идентификаторов ответов, текст которых указан в массиве $selectedAnswersText
     * @param $answers - Все возможные ответы на вопрос.
     * @param $selectedAnswersText - Текст выбранных ответов.
     * @return array
     */
    private function getAnswersIds($answers, $selectedAnswersText){

        $selectedAnswers = array_filter($answers, function($ans) use ($selectedAnswersText) {
            foreach ($selectedAnswersText as $answerText){
                if (strcmp($ans->text, $answerText) == 0) return true;
            }
            return false;
        });
        $selectedAnswersIds = array_map(function($ans){ return $ans->id;}, $selectedAnswers);

        return array_values($selectedAnswersIds);
    }

    private function askForConfirmation(){
        $warningMessage   =  "
                              ВНИМАНИЕ!
    Во время теста база данных и Redis Cache будут полностью очищены. 
    Убедитесь, что не используется основная база данных.";

        $this->writeConsoleMessage($warningMessage, 'red', 2);
        $this->writeConsoleMessage("Запустить тестирование полного процесса? [y/N] ",'white', 0);

        flush();
        ob_flush();
        $confirmation  =  trim( fgets( STDIN ) );
        if ( $confirmation !== 'y' ) {
            exit (0);
        }
    }























}