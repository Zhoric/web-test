<?php
use Repositories\UnitOfWork;
use CodeQuestionEngine\EngineGlobalSettings;
use CodeQuestionEngine\CCodeFileManager;


abstract class CodeFileManagerBase
{
    protected $app_path;

    protected $_uow;

    protected $dirPath;

    /**
     * Путь к шаблонному скрипту для конкретного языка
     */
    protected $scriptName;
    /**
     * Путь к кеш-папке
     */
    protected $cacheDirName;

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
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * @param mixed $scriptName
     */
    public function setScriptName($scriptName)
    {
        $this->scriptName = $scriptName;
    }



    public function __construct(UnitOfWork $uow)
    {
        $this->app_path = app_path();
        $this->_uow = $uow;
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


    public abstract function putCodeInFile($code);

    /**
     * Метод берет базовый шелл-скрипт и создает на его основе скрипт,который запускает
     * на выполнение код, лежащий в уникальной папке пользователя.
     * @throws \Exception
     */
    public abstract function createShellScript();

    /**
     * Создает шелл скрипт для прогона тестовых случаев
     * @param $casesCount - количество тестовых случаев
     * @param  $programId - id программы
     * @throws
     */
    public abstract function createShellScriptForTestCases($programId, $casesCount);

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

    public function utf8_urldecode($str)
    {
        $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
        return html_entity_decode($str, null, 'UTF-8');
    }





}