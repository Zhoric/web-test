<?php
use Repositories\UnitOfWork;
use CodeQuestionEngine\EngineGlobalSettings;


abstract class CodeFileManagerBase
{
    protected $app_path;
    protected $_uow;

    public function __construct(UnitOfWork $uow)
    {
        $this->app_path = app_path();
        $this->_uow = $uow;
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
     * @param $dirPath
     */
    public function createEmptyInputFile($dirPath)
    {
        $fp = fopen("$dirPath/input.txt", "w");
        fclose($fp);
    }

    public function getDirNameFromFullPath($dirPath)
    {
        $splitted = explode("/", $dirPath);
        $name = array_pop($splitted);
        return $name;

    }

    /**
     * Создает пустой файл, в котором хранится лог
     * @param $dirPath
     */
    public function createLogFile($dirPath)
    {
        $fp = fopen("$dirPath/log.txt", "w");
        fclose($fp);
    }

    public function putLogInfo($dirPath, $info)
    {
        $fp = fopen("$dirPath/log.txt", "w");
        fwrite($fp, $info);
        fclose($fp);
    }

    public function getErrors($dirPath)
    {
        $errors = file_get_contents("$dirPath/errors.txt");
        return $errors;
    }

    public function getStudentResult($dirPath)
    {
        try {
            $result = file_get_contents("$dirPath/result.txt");
            return $result;
        } catch (Exception $exception) {
            return 0;
        }
    }

    /**
     * Возвращает результат тестового случая под номером $testCaseNum
     * Результат лежит в папке $dirPath
     * @param $dirPath
     * @param $testCaseNum
     * @return string
     */

    public function getCorrectResult($dirPath, $testCaseNum)
    {
        $result = file_get_contents("$dirPath/testCase$testCaseNum.txt");
        return $result;
    }

    /**
     * Создает файл с результатами работы программы студента для тестого прогона с номером $number.
     *
     * @param $dirPath - путь к уникальной папке пользователя
     * @param $number - номер тестового прогона
     * @param $result - результат работы программы студента
     */
    public function createResultFile($dirPath, $result, $number)
    {
        $fp = fopen("$dirPath/student_result_$number.txt", "w");
        fwrite($fp, $result);
        fclose($fp);
    }


    public abstract function putCodeInFile($code, $dirPath);

    /**
     * Метод берет базовый шелл-скрипт и создает на его основе скрипт,который запускает
     * на выполнение код, лежащий в уникальной папке пользователя.
     * @param $dirPath - путь к уникальной папке пользователя
     * @throws \Exception
     */
    public abstract function createShellScript($dirPath);

    /**
     * Создает шелл скрипт для прогона тестовых случаев
     * @param $dirPath - путь к уникальной папке пользователя
     * @param $casesCount - количество тестовых случаев
     * @param  $programId - id программы
     * @throws
     */
    public abstract function createShellScriptForTestCases($dirPath, $programId, $casesCount);

    /**
     * Метод, который создает в уникальной папке пользователя пару файлов с тестовыми случаями для определенной задачи
     *
     * @param $input - входные данные (например, 2+2)
     * @param $output - выходные данные (4)
     * @param $number - номер тестового случая
     * @param $dirPath - путь к уникальной папке пользователя
     */
    public function putTestCaseInFiles($input, $output, $number, $dirPath)
    {
        $fp = fopen("$dirPath/test_input_$number.txt", "w");
        fwrite($fp, $input);
        fclose($fp);

        $fp = fopen("$dirPath/test_output_$number.txt", "w");
        fwrite($fp, $output);
        fclose($fp);

    }


    /**
     * Метод, который создает в уникальной папке пользователя файлы для всех тестовых случаев определенной задачи
     * @param $programId
     * @param $dirPath - путь к уникальной папке пользователя
     * @return int $count - число полученных тестовых случаев
     */
    public function createTestCasesFiles($programId, $dirPath)
    {
        $paramsSets = $this->_uow->paramsSets()->getByProgram($programId);
        $count = count($paramsSets);
        for ($i = 0; $i < $count; $i++) {
            $this->putTestCaseInFiles($paramsSets[$i]->getInput(),
                $paramsSets[$i]->getExpectedOutput(),
                $i,
                $dirPath);
        }

        return $count;
    }

    public function createTestCasesFilesByParamsSetsArray(array $paramsSets, $dirPath)
    {
        $count = count($paramsSets);
        for ($i = 0; $i < $count; $i++) {
            $this->putTestCaseInFiles($paramsSets[$i]->getInput(),
                $paramsSets[$i]->getExpectedOutput(),
                $i,
                $dirPath);
        }

        return $count;
    }


    /**
     * Сравнивает эталонный результат тестового случая с результатом студента.
     * true - если они идентичны
     * false - если нет
     */
    public function compareOutputs($dirPath, $inputFileName, $outputFileName)
    {

        $input = file_get_contents("$dirPath/$inputFileName");
        $output = file_get_contents("$dirPath/$outputFileName");

        return $input == $output;

    }


    public function calculateMark($dirPath, $casesCount)
    {

        if ($casesCount == 0) {
            throw new Exception('Отсутствует тестовые параметры');
        }
        $right_count = 0;
        for ($i = 0; $i < $casesCount; $i++) {
            if ($this->compareOutputs($dirPath, "test_output_$i.txt", "student_result_$i.txt")) {
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
     * @param $dirPath
     * @param $casesCount
     * @return string
     *
     */
    public function getResultsForCompare($dirPath, $casesCount)
    {
        $info = '';

        for ($i = 0; $i < $casesCount; $i++) {
            $input = file_get_contents("$dirPath/test_input_$i.txt");
            $expected = file_get_contents("$dirPath/test_output_$i.txt");
            $student_output = file_get_contents("$dirPath/student_result_$i.txt");

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