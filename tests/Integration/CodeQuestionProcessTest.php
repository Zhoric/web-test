<?php
use Helpers\DateHelper;
use Illuminate\Support\Facades\DB;


/**
 * Тестирование полного процесса создания и прохождения теста студентом.
 * Запуск:
 *          cd /var/www/web-test
 *          phpunit --debug

 *     Настройки среды, которые могут повлиять на успешность теста:
 * -- серверное время - от времени зависит определение текущего семестра для студента, а следовательно список дисциплин, по которым студент может пройти тест в текущий момент.
 * -- работа Redis Cache - для корректной работы процесса тестирования обязателен запуск сервера Redis Cache.
 * -- наличие базы данных, указанной в файле .env с применением всех миграций.
 *
 */
class CodeQuestionProcessTest extends TestCase
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

    // Идентификаторы тестовых вопросов по дисциплине "WEB-технологии"
    private static $C_CODE_QUESTION_ID = 1;
    private static $PHP_CODE_QUESTION_ID = 2;
    private static $PASCAL_CODE_QUESTION_ID = 3;

    //Текст сообщений об ошибках
    private static $AUTHORISATION_REPEAT_ERROR = 'Вы уже вошли под другим пользователем!';
    private static $USER_NOT_EXISTS_ERROR = 'Такого пользователя не существует!';

    //Идентификатор сессии тестирования студента (заполняется при запуске теста студентом).
    private static $TEST_SESSION_ID;

    //Константы процесса тестирования.
    private static $QUESTIONS_COUNT = 3;

    public function testFullProcess(){
        $this->writeConsoleMessage(PHP_EOL.'---- ЗАПУСК ТЕСТИРОВАНИЯ ДВИЖКА С ВОПРОСАМИ С КОДОМ. -----', 'cyan', 2);

        $this->createInstitute();
        $this->createProfile();
        $this->createRoles();
        $this->createAdminAccount();

        $this->logIn(self::$ADMIN_EMAIL, self::$PASSWORD);
        $this->createDiscipline();
        $this->createStudyPlan();
        $this->addDisciplineToStudyPlan();
        $this->createLecturerWithDisciplineAttached();
        $this->logOut();

        $this->logIn(self::$LECTURER_EMAIL, self::$PASSWORD);
        $this->createGroupWithStudyPlanAttached();
        $this->createDisciplineTheme();
        $this->createCodeQuestionC();
        $this->createCodeQuestionPascal();
        $this->createCodeQuestionPHP();
        $this->createTest();
        $this->logOut();

        $this->sendStudentRegistrationApplication();

        $this->logIn(self::$LECTURER_EMAIL, self::$PASSWORD);
        $this->acceptStudentRegistrationApplication();
        $this->logOut();

        $this->logIn(self::$STUDENT_EMAIL, self::$PASSWORD);
        $this->startTest();
        $this->answerAllTestQuestions();
        $this->logOut();

        $this->writeConsoleMessage(PHP_EOL.'---- ТЕСТИРОВАНИЕ ВОПРОСОВ С КОДОМ ПРОШЛО УСПЕШНО. ----', 'green', 2);
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


    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->clearTables();
        $this->clearRedis();
    }

    /**
     * Очистка всех задействованных в тесте таблиц.
     */
    protected function clearTables(){
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
     * Создание открытого вопроса с кодом на языке С
     *
     */
    private function createCodeQuestionC(){

        $this->writeConsoleMessage('Создание вопроса теста с кодом на языке С. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$C_CODE_QUESTION_ID,
                'type' => QuestionType::WithProgram,
                'text' => 'Выведите Hello world на языке С',
                'complexity' => QuestionComplexity::Low,
                'time' => 20],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "program" => '#include <stdio.h> int main(){printf("Hello world");}',
                "timeLimit" => '1',
                "memoryLimit" => '1',
                "lang" => "C",
                "paramSets" => [
                    ["input" => 2, "expectedOutput" => "Hello World"]
                ],
            ])
            ->seeJson(['Success' => true]);


        $this->writeOk();

    }


    private function createCodeQuestionPHP(){

        $this->writeConsoleMessage('Создание вопроса теста с кодом на языке PHP. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$PHP_CODE_QUESTION_ID,
                'type' => QuestionType::WithProgram,
                'text' => 'Выведите Hello world на языке PHP',
                'complexity' => QuestionComplexity::Low,
                'time' => 20],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "program" => "echo 'Hello world' ",
                "timeLimit" => '1',
                "memoryLimit" => '1',
                "lang" => "PHP",
                "paramSets" => [
                    ["input" => 2, "expectedOutput" => "Hello World"]
                ],
            ])
            ->seeJson(['Success' => true]);


        $this->writeOk();
    }


    private function createCodeQuestionPascal(){

        $this->writeConsoleMessage('Создание вопроса теста с кодом на языке Pascal. [API]');
        $apiUri = '/api/questions/create';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri,
            [ "question" => [
                'id' => self::$PASCAL_CODE_QUESTION_ID,
                'type' => QuestionType::WithProgram,
                'text' => 'Выведите Hello world на языке Pascal',
                'complexity' => QuestionComplexity::Low,
                'time' => 20],
                "theme" => self::$HTML_DISCIPLINE_THEME_ID,
                "program" => "begin writeln ('Hello, world.'); end.",
                "timeLimit" => '1',
                "memoryLimit" => '1',
                "lang" => "Pascal",
                "paramSets" => [
                    ["input" => 2, "expectedOutput" => "Hello World"]
                ],
            ])
            ->seeJson(['Success' => true]);


        $this->writeOk();
    }



    /**
     * Проверка доступа к директории.
     * @param $fullPath - полный путь к директории.
     */
    protected function checkDirectoryAccess($fullPath){
        $this->writeConsoleMessage('Проверка доступа к директории '.$fullPath.' [DIR]', 'grey', 1);

        $this->assertFileExists($fullPath);
        $this->assertFileIsReadable($fullPath);
        $this->assertFileIsWritable($fullPath);
    }

    /**
     * Проверка работы сервера Redis Cache.
     */
    protected function checkRedisServerIsRunning(){
        $this->writeConsoleMessage('Проверка подключения к серверу Redis Cache [Redis]', 'grey', 1);
        $serverResponse = $this->getRedisClient()->connection()->ping();
        $this->assertEquals('PONG', $serverResponse);
    }

    /**
     * Очистка хранилища Redis Cache.
     */
    protected function clearRedis(){
        $this->writeConsoleMessage('Очистка хранилища Redis Cache. [Redis]', 'grey', 1);
        $this->getRedisClient()->connection()->flushall();
    }

    /**
     * Добавление ролей пользователей в БД.
     */
    protected function createRoles(){
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
    protected function createInstitute(){
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
    protected function createAdminAccount(){
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
    protected function logIn($email, $password){
        $this->writeNewLine();
        $this->writeConsoleMessage('Авторизация с email: '.$email.' и паролем: '.$password.'. [API]', 'blue');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST',$apiUri , ['email' => $email, 'password' => $password])
            ->seeJson(['Success' => true]);
        $this->writeOk();
    }

    protected function logOut(){
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
    protected function checkRepeatableLoginDisabled($email, $password){
        $this->writeConsoleMessage('Проверка отсутствия возможности повторной авторизации. [API]');
        $apiUri = '/login';
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri, ['email' => $email, 'password' => $password])
            ->seeJson(['Success' => false, 'Message' => self::$AUTHORISATION_REPEAT_ERROR]);
        $this->writeOk();
    }

    protected function checkWrongCredentialsAuthorization($email, $password){
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
    protected function createProfile(){
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
    protected function createStudyPlan(){
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
    protected function createDiscipline(){
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
    protected function addDisciplineToStudyPlan(){
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
    protected function createLecturerWithDisciplineAttached(){
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
    protected function createGroupWithStudyPlanAttached(){
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
    protected function createDisciplineTheme(){
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



    /*
     * Создание теста по теме "HTML" дисциплины "WEB-технологии".
     */
    protected function createTest(){
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
    protected function sendStudentRegistrationApplication(){
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
     * Подтверждение преподавателем заявки студента на регистрацию.
     */
    protected function acceptStudentRegistrationApplication(){
        $this->writeConsoleMessage('Подтверждение преподавателем заявки студента на регистрацию. [API]');
        $apiUri = '/api/user/activate/'.self::$STUDENT_ID;
        $this->writeApiCall($apiUri);

        $this->json('POST', $apiUri)
            ->seeJson(['Success' => true]);

        $this->seeInDatabase('user', ['email' => self::$STUDENT_EMAIL, 'active' => true]);

        $this->writeOk();
    }


    /**
     * Запуск процесса тестирования.
     */
    protected function startTest(){
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
     * Последовательное получение и отправка ответа на все вопросы теста.
     */
    protected function answerAllTestQuestions(){
        $questionsAnswered = 0;

        // Вопросы будут запрашиваться до тех пор, пока с сервера не вернётся результат теста.
        while (true) {
            $questionData = $this->getNextTestQuestion();
            if (!array_key_exists('question', $questionData)) break;

            $answerData = $this->createQuestionAnswerData($questionData);
            $this->answerTestQuestion($answerData);
            $questionsAnswered++;
        }

        $this->writeConsoleMessage('Проверка общего количества полученных вопросов теста.');
        $this->assertEquals(self::$QUESTIONS_COUNT, $questionsAnswered);
        $this->writeOk();
    }

    /**
     * Получение очередного вопроса студентом.
     */
    protected function getNextTestQuestion(){
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
    protected function createQuestionAnswerData($questionData){
        $question = $questionData->question;
        $answers = $questionData->answers;

        $answerData = [
            'questionId' => $question->id,
            'answerIds' => [],
            'answerText' => ''
        ];

        // Заполняем объект ответа на вопрос в соответствии с вопросом.
        switch ($question->id){
            case self::$C_CODE_QUESTION_ID:{

                break;
            }
            case self::$PASCAL_CODE_QUESTION_ID:{

                break;
            }
            case self::$PHP_CODE_QUESTION_ID:{

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
    protected function answerTestQuestion($answerData){
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
     * Выбор идентификаторов ответов, текст которых указан в массиве $selectedAnswersText
     * @param $answers - Все возможные ответы на вопрос.
     * @param $selectedAnswersText - Текст выбранных ответов.
     * @return array
     */
    protected function getAnswersIds($answers, $selectedAnswersText){

        $selectedAnswers = array_filter($answers, function($ans) use ($selectedAnswersText) {
            foreach ($selectedAnswersText as $answerText){
                if (strcmp($ans->text, $answerText) == 0) return true;
            }
            return false;
        });
        $selectedAnswersIds = array_map(function($ans){ return $ans->id;}, $selectedAnswers);

        return array_values($selectedAnswersIds);
    }

    protected function askForConfirmation(){
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