<?php

use CodeQuestionEngine\CodeFileManagerFactory;


class CodeFileManagerTest extends TestCase
{
    /**
     * @var CodeFileManager
     */
    private $codeFileManager;

    /**
     * @var User
     */
    private $user;


    private $dirPath;


    /**
     * Действия, которые будут выполнены перед запуском теста.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->codeFileManager = CodeFileManagerFactory::getCodeFileManager(Language::C);
        $user = new User();
        $user->setFirstname("Кирилл");
        $user->setLastname("Сухоруких");
        $user->setPatronymic("Всеволодович");
        $this->user = $user;

        $dirName = $user->getFirstname() . "_" .
            $user->getLastname() . "_" .
            $user->getPatronymic();

        $app_path = app_path();
        $cacheDir = $this->codeFileManager->getCacheDirName();

        $this->dirPath = "$app_path/$cacheDir/$dirName";

        $this->codeFileManager->createNonUniqueDir($user);

    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        $this->codeFileManager->removeDir($this->dirPath);
        parent::tearDown();
    }


    /**
     * Создание входного файла для кода
     */
    public function testCreateInputFile()
    {
        $this->writeConsoleMessage('Cоздание входного файла ');
        //Arrange

        $filePath = $this->codeFileManager->getDirPath()."/".
                    $this->codeFileManager->getInputFileName();

        //Act
        $this->codeFileManager->createInputFile();

        //Assert
        $this->assertEquals(true, file_exists($filePath));
        $this->writeOk();
    }


    /**
     *  Создание директории
     */
    public function testCreateDir(){

        $this->writeConsoleMessage('Cоздание директории ');

        //Arrange

        //Act
        $path = $this->codeFileManager->createDir($this->user);

        //Assert
        $this->assertEquals(true,file_exists($path));
        rmdir($path);
        $this->writeOk();
    }

    /**
     * Удаление директории
     */
    public function testRemoveDir(){
        $this->writeConsoleMessage('Удаление директории ');

        //Arrange
        $path = $this->codeFileManager->createDir($this->user);

        //Act
        $this->codeFileManager->removeDir($path);

        //Assert
        $this->assertEquals(false,file_exists($path));
        $this->writeOk();
    }

    /**
     * Создание шелл-скрипта
     */
    public function testCreateShellScript(){
        $this->writeConsoleMessage('Cоздание шелл-скрипта');
        //Arrange
        $executeShellScriptName = $this->codeFileManager->getBaseShellScriptName();
        $uniqueDirName = $this->dirPath;
        $filePath = "$uniqueDirName/$executeShellScriptName";


        //Act

        $this->codeFileManager->createShellScript();
        $this->assertEquals(true,file_exists($filePath));
        $this->writeOk();

    }


    /**
     * Создание шелл-скрипта для тестового случая(проверка имени созданного файла)
     */
    public function testCreateShellScriptForTestCase(){

        $this->writeConsoleMessage('Создание шелл-скрипта для тестового случая');

        //Arrange
        $program_id = 1;
        $test_case_num = 0;

        $scriptName = $this->codeFileManager->getBaseShellScriptName();
        $executeName = $this->codeFileManager->getExecuteFileName();

        $splitted = explode(".", $scriptName);
        $first_part = $splitted[0].$test_case_num.".";
        $second_part = $splitted[1];


        $splitted = explode(".",$executeName);
        $first_part1 = $splitted[0]."_".$program_id."_".$test_case_num;

        if(count($splitted ) > 1) {
            $second_part1 = $splitted[1];
            $first_part1.=".";
        }
        else{
            $second_part1="";
        }
        //Act

        $result = $this->codeFileManager->CreateShellScriptForTestCase($program_id,$test_case_num);
        //Assert

        self::assertEquals($first_part.$second_part,$result["scriptName"]);
        self::assertEquals($first_part1.$second_part1,$result["executeFileName"]);
        $this->writeOk();


    }



}