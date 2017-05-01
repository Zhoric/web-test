<?php
use Repositories\UnitOfWork;
use CodeQuestionEngine\EngineGlobalSettings;


class CodeFileManager
{
    protected $app_path;

    protected $_uow;

    protected $dirPath;

    /**
     * Имя шаблонного скрипта для конкретного языка
     */
    protected $baseShellScriptName;
    /**
     * Путь к кеш-папке
     */
    protected $cacheDirName;

    /**
     * Имя входного файла с входными данными задачи
     */
    protected $inputFileName;

    /**
     * Имя выходного файла с результатами задачи
     */
    protected $outputFileName;

    /**
     * Имя входного файла с входными данными задачи для тестового случая
     */
    protected $inputFileNamePatternForTestCase;

    /**
     * Имя выходного файла с входными данными задачи для тестового случая
     */
    protected $outputFileNamePatternForTestCase;

    /**
     * Расширение файла для конкретного языка программирования
     */
    protected $codeFileExtension;

    /**
     * имя файла с кодом
     */
    protected $codeFileName;


    /**
     * имя уникальной папки пользователя
     */
    protected $uniqueDirName;

    /**
     * Ключевое слово в шаблонном шелл-скрипте, вместо которого подставляются настройки запуска
     */
    protected $keyWordToRun;

    /**
     * Имя исполняемого файла для конкретного языка
     */
    protected $executeFileName;

    /**
     * Директория, где хранятся базовые шелл-скрипты
     */
    protected $baseShellScriptDir;


    /**
     * Ключевое слово в шаблонном шелл-скрипте, вместо которого подставляется имя exe файла или выполняемого скрипта
     */
    protected $keyWordToPutObjectFile;


    /**
     * @return mixed
     */
    public function getExecuteFileName()
    {
        return $this->executeFileName;
    }

    /**
     * @param mixed $executeFileName
     */
    public function setExecuteFileName($executeFileName)
    {
        $this->executeFileName = $executeFileName;
    }

    /**
     * @return mixed
     */
    public function getCodeFileExtension()
    {
        return $this->codeFileExtension;
    }

    /**
     * @param mixed $codeFileExtension
     */
    public function setCodeFileExtension($codeFileExtension)
    {
        $this->codeFileExtension = $codeFileExtension;
    }

    /**
     * @return mixed
     */
    public function getCacheDirName()
    {
        return $this->cacheDirName;
    }

    /**
     * @param mixed $cacheDirName
     */
    public function setCacheDirName($cacheDirName)
    {
        $this->cacheDirName = $cacheDirName;
    }


    /**
     * @return mixed
     */
    public function getBaseShellScriptName()
    {
        return $this->baseShellScriptName;
    }

    /**
     * @param mixed $baseShellScriptName
     */
    public function setBaseShellScriptName($baseShellScriptName)
    {
        $this->baseShellScriptName = $baseShellScriptName;
    }

    /**
     * @return mixed
     */
    public function getDirPath()
    {
        return $this->dirPath;
    }

    /**
     * @param mixed $dirPath
     */
    public function setDirPath($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    public function __construct(UnitOfWork $uow)
    {
        $this->app_path = app_path();
        $this->_uow = $uow;
        $this->baseShellScriptDir = EngineGlobalSettings::BASE_SHELL_SCRIPT_DIR_NAME;
        $this->inputFileName  = EngineGlobalSettings::INPUT_FILE_NAME;
        $this->outputFileName = EngineGlobalSettings::OUTPUT_FILE_NAME;
        $this->inputFileNamePatternForTestCase = EngineGlobalSettings::INPUT_FILE_NAME_FOR_TEST_CASE;
        $this->outputFileNamePatternForTestCase = EngineGlobalSettings::OUTPUT_FILE_NAME_FOR_TEST_CASE;
        $this->codeFileName = EngineGlobalSettings::CODE_FILE_NAME;
        $this->keyWordToRun = EngineGlobalSettings::KEY_WORD_TO_PUT_RUN_INFO;
        $this->keyWordToPutObjectFile = EngineGlobalSettings::OBJECT_FILE_KEY_WORD;
    }

    /**
     * Создает директорию cо следующим именем: ФИО юзера и текущий unix_time
     * @param \User $user
     * @return string - путь к созданной папке
     * @throws \Exception
     *
     */
    public function createDir(User $user)
    {
        try {
            $dirName = $user->getFirstname() . "_" .
                $user->getLastname() . "_" .
                $user->getPatronymic() . "_" .
                time();
            $cacheDir = EngineGlobalSettings::CACHE_DIR;

            $dirPath = "$this->app_path/$cacheDir/$dirName";
            mkdir($dirPath, 0777);
        } catch (\Exception $e) {
            throw new \Exception("Не удалось создать директорию!");
        }
        $this->setDirPath($dirPath);
        $this->uniqueDirName = $this->getDirNameFromFullPath();
        return $dirPath;
    }

    /**
     * Создает пустой файл для входных параметров
     */
    public function createEmptyInputFile()
    {
        $fp = fopen("$this->dirPath/input.txt", "w");
        fclose($fp);
    }

    public function getDirNameFromFullPath()
    {
        $splitted = explode("/", $this->dirPath);
        $name = array_pop($splitted);
        return $name;

    }

    /**
     * Создает пустой файл, в котором хранится лог
     */
    public function createLogFile()
    {
        $fp = fopen("$this->dirPath/log.txt", "w");
        fclose($fp);
    }

    public function putLogInfo($info)
    {
        $fp = fopen("$this->dirPath/log.txt", "w");
        fwrite($fp, $info);
        fclose($fp);
    }

    public function getErrors()
    {
        $errors = file_get_contents("$this->dirPath/errors.txt");
        return $errors;
    }

    public function getStudentResult()
    {
        try {
            $result = file_get_contents("$this->dirPath/result.txt");
            return $result;
        } catch (Exception $exception) {
            return 0;
        }
    }

    /**
     * Возвращает результат тестового случая под номером $testCaseNum
     * Результат лежит в папке $dirPath
     * @param $testCaseNum
     * @return string
     */

    public function getCorrectResult($testCaseNum)
    {
        $result = file_get_contents("$this->dirPath/testCase$testCaseNum.txt");
        return $result;
    }

    /**
     * Создает файл с результатами работы программы студента для тестого прогона с номером $number.
     *
     * @param $number - номер тестового прогона
     * @param $result - результат работы программы студента
     */
    public function createResultFile($result, $number)
    {
        $fp = fopen("$this->dirPath/student_result_$number.txt", "w");
        fwrite($fp, $result);
        fclose($fp);
    }


    public function putCodeInFile($code){
        $fileName = str_replace("*",$this->codeFileExtension, $this->codeFileName);
        $fp = fopen("$this->dirPath/$fileName", "w");
        fwrite($fp, $code);
        fclose($fp);
    }

    /**
     * Метод, который создает в уникальной папке пользователя пару файлов с тестовыми случаями для определенной задачи
     *
     * @param $input - входные данные (например, 2+2)
     * @param $output - выходные данные (4)
     * @param $number - номер тестового случая
     */
    public function putTestCaseInFiles($input, $output, $number)
    {
        $fp = fopen("$this->dirPath/test_input_$number.txt", "w");
        fwrite($fp, $input);
        fclose($fp);

        $fp = fopen("$this->dirPath/test_output_$number.txt", "w");
        fwrite($fp, $output);
        fclose($fp);

    }

    /**
     * Метод, который создает в уникальной папке пользователя файлы для всех тестовых случаев определенной задачи
     * @param $programId
     * @return int $count - число полученных тестовых случаев
     */
    public function createTestCasesFiles($programId)
    {
        $paramsSets = $this->_uow->paramsSets()->getByProgram($programId);
        $count = count($paramsSets);
        for ($i = 0; $i < $count; $i++) {
            $this->putTestCaseInFiles($paramsSets[$i]->getInput(),
                $paramsSets[$i]->getExpectedOutput(),
                $i);
        }

        return $count;
    }

    public function createTestCasesFilesByParamsSetsArray(array $paramsSets)
    {
        $count = count($paramsSets);
        for ($i = 0; $i < $count; $i++) {
            $this->putTestCaseInFiles($paramsSets[$i]->getInput(),
                $paramsSets[$i]->getExpectedOutput(),
                $i);
        }

        return $count;
    }

    /**
     * Сравнивает эталонный результат тестового случая с результатом студента.
     * true - если они идентичны
     * false - если нет
     */
    public function compareOutputs($inputFileName, $outputFileName)
    {
        $input = file_get_contents("$this->dirPath/$inputFileName");
        $output = file_get_contents("$this->dirPath/$outputFileName");

        return $input == $output;
    }


    public function calculateMark($casesCount)
    {
        if ($casesCount == 0) {
            throw new Exception('Отсутствует тестовые параметры');
        }
        $right_count = 0;
        for ($i = 0; $i < $casesCount; $i++) {
            if ($this->compareOutputs("test_output_$i.txt", "student_result_$i.txt")) {
                $right_count++;
            }
        }


        return floor(($right_count / $casesCount) * 100);
    }

    /**
     * Метод, возвращающий текстовую информацию о результатах теста в формате
     * Тестовый случай №:
     * Входные параметры:
     * Ожидаемый вывод:
     * Вывод студента:
     * @param $casesCount
     * @return string
     *
     */
    public function getResultsForCompare($casesCount)
    {
        $info = '';

        for ($i = 0; $i < $casesCount; $i++) {
            $input = file_get_contents("$this->dirPath/test_input_$i.txt");
            $expected = file_get_contents("$this->dirPath/test_output_$i.txt");
            $student_output = file_get_contents("$this->dirPath/student_result_$i.txt");

            $info .= "Тестовый случай №:$i\n";
            $info .= "Входные параметры:\n$input\n";
            $info .= "Ожидаемый вывод:\n$expected\n";
            $info .= "Вывод студента:\n$student_output\n";
        }

        return $info;


    }

    protected function getBaseShellScriptText(){

        $cache_dir = $this->baseShellScriptDir;

        $sh_name = $this->getBaseShellScriptName();
        $shPath = "$this->app_path/$cache_dir/$sh_name";

        $baseShellScript = fopen($shPath, "r"); // открываем для чтения
        $text = fread($baseShellScript, filesize($shPath)); //читаем
        fclose($baseShellScript);

        return $text;

    }


    protected function getExecutionFileNameIfExist(){

        $dir_items  = scandir($this->dirPath);
        $name = explode(".",$this->executeFileName)[0];
        foreach($dir_items as $item){
            if(strstr($item,$name)){
                return $item;
            }
        }
        return "";
    }

    protected function renameFile($old,$new){
        rename($this->dirPath."/".$old
            ,$this->dirPath."/".$new);
    }

    protected function putBaseShellScriptInfoIntoExecuteShellScript($executeShellScriptName, $executeFileName){

        $filePath = "$this->app_path/$this->cacheDirName/$this->uniqueDirName/$executeShellScriptName";

        $uniqueScript = fopen($filePath, "w");

        $command = "cd /opt/$this->cacheDirName/$this->uniqueDirName/\n";

        $alreadyExistedExecutionFile = $this->getExecutionFileNameIfExist();

        if($alreadyExistedExecutionFile == ""){

        $text = $this->getBaseShellScriptText();
        $text = str_replace($this->keyWordToPutObjectFile, $executeFileName,$text);
            
        }
        else{
            $this->renameFile($alreadyExistedExecutionFile, $executeFileName);
            $text = $this->keyWordToRun;
        }

        $command = $command . $text;

        fwrite($uniqueScript, $command);
        fclose($uniqueScript);

        return $filePath;
    }

    protected function createShellScriptNameForTestCase($testCaseNumber){
        $shellScriptNameForTestCase = $this->getBaseShellScriptName();

        $splitted = explode(".", $shellScriptNameForTestCase);
        $first_part = $splitted[0].$testCaseNumber;
        $second_part = $splitted[1];
        $shellScriptNameForTestCase = $first_part.".".$second_part;
        return $shellScriptNameForTestCase;
    }

    protected function getInputFileNameForTestCase($testCaseNum){
       return str_replace("*",$testCaseNum,$this->inputFileNamePatternForTestCase);
    }

    protected function getOutputFileNameForTestCase($testCaseNum){
        return str_replace("*",$testCaseNum,$this->outputFileNamePatternForTestCase);
    }

    protected function getExecuteFileNameForTestCase($programId,$testCaseNum){
        $executeFileNameForTestCase = $this->getExecuteFileName();

        $splitted = explode(".", $executeFileNameForTestCase);
        $first_part = $splitted[0].'_'.$programId.'_'.$testCaseNum;
        $second_part = $splitted[1];
        $executeFileNameForTestCase = $first_part.".".$second_part;
        return $executeFileNameForTestCase;
    }


    protected function CreateCommandStringToExecute($executeFileName,$inputFileName,$outputFileName){

        $executeCommand = EngineGlobalSettings::EXECUTE_PATTERN;
        $executeCommand = str_replace("$1", $executeFileName, $executeCommand);
        $executeCommand = str_replace("$2", $outputFileName, $executeCommand);
        $executeCommand = str_replace("$3", $inputFileName,$executeCommand);

        return $executeCommand;
    }

    /**
     * Создает шелл скрипт для запуска программы на выполнение
     * @throws Exception
     */
    public function createShellScript(){

        try {
            $filePath =  $this->putBaseShellScriptInfoIntoExecuteShellScript($this->getBaseShellScriptName()
                , $this->executeFileName);
            $testShellScriptText = file_get_contents($filePath);
            $command = $this->CreateCommandStringToExecute($this->executeFileName
                ,$this->inputFileName
                , $this->outputFileName);
            $text = str_replace($this->keyWordToRun, $command, $testShellScriptText);
            file_put_contents($filePath, $text);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }

    }

    /**
     * Создает шелл-скрипт для для запуска программы для тестовых случаев
     * @param $programId
     * @throws \Exception
     * @param $testCaseNum
     * @return string
     */
    public function CreateShellScriptForTestCase($programId,$testCaseNum)
    {
        try {
            $scriptName = $this->createShellScriptNameForTestCase($testCaseNum);
            $executeFileName = $this->getExecuteFileNameForTestCase($programId, $testCaseNum);
            $filePath = $this->putBaseShellScriptInfoIntoExecuteShellScript($scriptName, $executeFileName);
            $testShellScriptText = file_get_contents($filePath);
            $outputFileName = $this->getOutputFileNameForTestCase($testCaseNum);
            $inputFileName = $this->getInputFileNameForTestCase($testCaseNum);
            $command = $this->CreateCommandStringToExecute($executeFileName, $inputFileName, $outputFileName);
            $text = str_replace($this->keyWordToRun, $command, $testShellScriptText);
            file_put_contents($filePath, $text);

            return $scriptName;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }
    }









}