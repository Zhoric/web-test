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
     * @return mixed
     */
    public function getCodeFileName()
    {
        return str_replace("*",$this->codeFileExtension, $this->codeFileName);
    }


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
     * имя файла с ожидаемым результатом
     */
    protected $expecedOutputFileName;

    /**
     * файл с ошибками времени компиляции
     */
    protected $errorsFileName;

    /**
     * служебное слово для запуска
     */
    protected $runWord;

    /**
     * @return mixed
     */
    public function getRunWord()
    {
        return $this->runWord;
    }

    /**
     * @param mixed $runWord
     */
    public function setRunWord($runWord)
    {
        $this->runWord = $runWord;
    }


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

    public function createInputFile(){
        $fp = fopen("$this->dirPath/$this->inputFileName", "w");
        fclose($fp);
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
        $this->inputFileNamePatternForTestCase = EngineGlobalSettings::INPUT_FILE_NAME_FOR_TEST_CASE;
        $this->outputFileNamePatternForTestCase = EngineGlobalSettings::OUTPUT_FILE_NAME_FOR_TEST_CASE;
        $this->codeFileName = EngineGlobalSettings::CODE_FILE_NAME;
        $this->keyWordToRun = EngineGlobalSettings::KEY_WORD_TO_PUT_RUN_INFO;
        $this->keyWordToPutObjectFile = EngineGlobalSettings::OBJECT_FILE_KEY_WORD;
        $this->expecedOutputFileName = EngineGlobalSettings::OUTPUT_FILE_FOR_EXPECTED_RESULT;
        $this->errorsFileName = EngineGlobalSettings::ERRORS_FILE;
        $this->inputFileName = EngineGlobalSettings::INPUT_FILE_NAME;
        $this->outputFileName = EngineGlobalSettings::OUTPUT_FILE_NAME;
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
        $errors = file_get_contents("$this->dirPath/$this->errorsFileName");
        return $errors;
    }



    public function putCodeInFile($code){
        $fileName = $this->getCodeFileName();
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
    public function putTestCaseInFiles($input, $output, $number){

        $inputFileName = str_replace("*",$number,$this->getInputFileNameForTestCase($number));
        $fp = fopen("$this->dirPath/$inputFileName", "w");
        fwrite($fp, $input);
        fclose($fp);

        $outputFileName = str_replace("*",$number,$this->expecedOutputFileName);
        $fp = fopen("$this->dirPath/$outputFileName", "w");
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


    /**
     * @param array $codeTasks
     * @return float
     * @throws Exception
     */
    public function calculateMark(array $codeTasks)
    {
        $casesCount = count($codeTasks);

        if ($casesCount == 0) {
            throw new Exception('Отсутствует тестовые параметры');
        }
        $right_count = 0;


        for ($i = 0; $i < $casesCount; $i++) {

            if($codeTasks[$i]->state == CodeTaskStatus::MemoryOverflow ||
                $codeTasks[$i]->state == CodeTaskStatus::Timeout){
                echo "задача умерла по таймауту";
                continue;
            }

            $expectedOutputFileName = str_replace("*",$i,$this->expecedOutputFileName);
            $outputFileName = str_replace("*",$i,$this->getOutputFileNameForTestCase($i));

            if ($this->compareOutputs($expectedOutputFileName, $outputFileName)) {
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
            $inputFileName = str_replace("*",$i,$this->inputFileName);
            $expectedOutputFileName = str_replace("*",$i,$this->expecedOutputFileName);
            $outputFileName = str_replace("*",$i,$this->outputFileName);
            $input = file_get_contents("$this->dirPath/$inputFileName");
            $expected = file_get_contents("$this->dirPath/$expectedOutputFileName");
            $student_output = file_get_contents("$this->dirPath/$outputFileName");

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
        if($this->executeFileName === ""){
            $this->executeFileName = $this->getCodeFileName();
        }
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

        if($executeFileNameForTestCase === ""){
            return $this->getCodeFileName();
        }

        $splitted = explode(".", $executeFileNameForTestCase);
        $first_part = $splitted[0].'_'.$programId.'_'.$testCaseNum;
        $second_part = $splitted[1];
        $executeFileNameForTestCase = $first_part.".".$second_part;
        return $executeFileNameForTestCase;
    }


    protected function CreateCommandStringToExecute($executeFileName,$inputFileName,$outputFileName,$isScriptLang){

        if(!$isScriptLang) {
            $executeCommand = EngineGlobalSettings::EXECUTE_PATTERN;
        }
        else {
            $executeCommand = EngineGlobalSettings::EXECUTE_PATTERN_FOR_SCRIPT_LANGUAGES;
        }
            $executeCommand = str_replace("$0", $this->getRunWord(), $executeCommand);
            $executeCommand = str_replace("$1", $executeFileName, $executeCommand);
            $executeCommand = str_replace("$2", $outputFileName, $executeCommand);
            $executeCommand = str_replace("$3", $inputFileName, $executeCommand);

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

            //todo:: доделать
            $command = $this->CreateCommandStringToExecute($this->executeFileName
                ,$this->inputFileName
                , $this->outputFileName,false);
            $text = str_replace($this->keyWordToRun, $command, $testShellScriptText);
            file_put_contents($filePath, $text);

            return ["scriptName" => $this->getBaseShellScriptName(), "executeFileName" => $this->executeFileName];
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
     * @return array
     */
    public function CreateShellScriptForTestCase($programId,$testCaseNum)
    {
        try {
            $scriptName = $this->createShellScriptNameForTestCase($testCaseNum);

            $isScriptLang = false;
            if($this->executeFileName === ""){
                $isScriptLang = true;
            }

            $executeFileNameForTestCase = $this->getExecuteFileNameForTestCase($programId, $testCaseNum);


            $filePath = $this->putBaseShellScriptInfoIntoExecuteShellScript($scriptName, $executeFileNameForTestCase);


            $testShellScriptText = file_get_contents($filePath);

            $outputFileName = $this->getOutputFileNameForTestCase($testCaseNum);
            $inputFileName = $this->getInputFileNameForTestCase($testCaseNum);
            $command = $this->CreateCommandStringToExecute($executeFileNameForTestCase
                , $inputFileName
                , $outputFileName
                , $isScriptLang);


            $text = str_replace($this->keyWordToRun, $command, $testShellScriptText);
            file_put_contents($filePath, $text);

            return ["scriptName" => $scriptName, "executeFileName" => $executeFileNameForTestCase];
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            throw new \Exception("Ошибка при создании скрипта: $msg");
        }
    }









}