<?php

namespace Managers;

use Answer;
use Discipline;
use Exception;
use ExportResultViewModel;
use Helpers\DateHelper;
use Helpers\FileHelper;
use ImportResultViewModel;
use Question;
use QuestionComplexity;
use QuestionType;
use Repositories\UnitOfWork;
use Test;
use TestTheme;
use TestType;
use Theme;

class OldDbImportManager
{
    /**
     * Максимальная длина текста вопроса при отображении вопроса в информационном сообщении.
     * Используется чтобы обрезать текст вопроса, с которым при экспорте/импорте что-то пошло не так.
     * @var int
     */
    private static $questionTextMaxLength = 100;

    /**
     * Директория для импорта/экспорта вопросов.
     * /public/import
     * @var string
     */
    public static $importPath = 'import/';

    /**
     * @var string
     */
    public static $importFileName = 'db_export.xml';


    /**
     * Тип файлов для импорта вопросов.
     * @var string
     */
    public static $importFileType = 'xml';

    private $_questionManager;
    private $_disciplineManager;
    private $_testManager;
    private $_unitOfWork;

    public function __construct(QuestionManager $questionManager,
                                DisciplineManager $disciplineManager,
                                TestManager $testManager,
                                UnitOfWork $unitOfWork)
    {
        $this->_questionManager = $questionManager;
        $this->_disciplineManager = $disciplineManager;
        $this->_testManager = $testManager;
        $this->_unitOfWork = $unitOfWork;
    }

    public function importQuestions()
    {
        $importFilePath = self::$importPath . self::$importFileName;
        $fileContent = file_get_contents($importFilePath);
        $importResult = new ImportResultViewModel();

        $disciplinesData = $this->getTextBetweenTags($fileContent, 'discipline');
        $themesData = $this->getTextBetweenTags($fileContent, 'theme');
        $testsData = $this->getTextBetweenTags($fileContent, 'test');
        $testsThemesData = $this->getTextBetweenTags($fileContent, 'test-theme');
        $questionsData = $this->getTextBetweenTags($fileContent, 'question');

        $this->saveDisciplines($disciplinesData, $importResult);
        $this->saveThemes($themesData, $importResult);
        $this->saveTests($testsData, $importResult);
        $this->saveTestThemes($testsThemesData, $importResult);
        $this->saveQuestions($questionsData, $importResult);

        return $importResult;
    }

    public function saveDisciplines($disciplinesData, $importResult){
        foreach ($disciplinesData as $discipline){
            $this->parseAndSaveDiscipline($discipline, $importResult);
        }
    }

    public function saveThemes($themesData, $importResult){
        foreach ($themesData as $theme){
            $this->parseAndSaveTheme($theme, $importResult);
        }
    }

    public function saveTests($testsData, $importResult){
        foreach ($testsData as $test){
            $this->parseAndSaveTest($test, $importResult);
        }
    }

    public function saveTestThemes($testThemesData, $importResult){
        foreach ($testThemesData as $testTheme){
            $this->parseAndSaveTestTheme($testTheme, $importResult);
        }
    }

    public function saveQuestions($questionsData, $importResult){
        foreach ($questionsData as $question) {
            $this->parseAndSaveQuestion($question, $importResult);
        }
    }


    /**
     * Урезание текста вопроса для вывода в информационных сообщениях о процессе импорта/экспорта.
     * @param $text
     * @return string
     */
    private function cutQuestionText($text)
    {
        return (strlen($text) >= self::$questionTextMaxLength)
            ? substr($text, 0, self::$questionTextMaxLength) . '...'
            : $text;
    }

    /**
     * Получение текста, находящегося между двумя указанными тегами (независимо от наличия переносов строки).
     * @param $string - строка, в которой предполагается нахождение теста с заданными тегами.
     * @param $tagname - Сам тег. Например, <question>. Указывается без скобок.
     * @param bool $takeFirst - Признак того, что из всех совпадений стоит вернуть лишь первое.
     * @return null
     */
    function getTextBetweenTags($string, $tagname, $takeFirst = false)
    {
        $pattern = '/<' . $tagname . '>(.*?)<\/' . $tagname . '>/ism';
        preg_match_all($pattern, $string, $matches);
        $matchedStrings =  $matches[1];

        if ($takeFirst){
            return (isset($matchedStrings) && !empty($matchedStrings)) ? $matchedStrings[0] : null;
        }
        return $matchedStrings;
    }

    function parseAndSaveDiscipline($disciplineData, ImportResultViewModel $importResult){
        try{
            $discipline = new Discipline();

            $id = $this->getTextBetweenTags($disciplineData, 'd-id', true);
            $this->validateNodeNotEmpty($id, 'Id дисциплины');

            $name = $this->getTextBetweenTags($disciplineData, 'd-name', true);
            $this->validateNodeNotEmpty($name, 'Название дисциплины '.$id);

            $discipline->setId($id)->setName($name)->setAbbreviation($name);
            $this->_disciplineManager->addDiscipline($discipline, []);

        } catch (Exception $exception){
            array_push($importResult->errors, $exception->getMessage());
            $importResult->failed++;
        }
    }

    function parseAndSaveTheme($themeData, ImportResultViewModel $importResult){
        try{
            $theme = new Theme();

            $id = $this->getTextBetweenTags($themeData, 't-id', true);
            $this->validateNodeNotEmpty($id, 'Id темы');

            $discipline = $this->getTextBetweenTags($themeData, 't-discipline', true);
            $this->validateNodeNotEmpty($discipline, 'Id дисциплины, к которой принадлежит тема'.$id);

            $name = $this->getTextBetweenTags($themeData, 't-name', true);
            $this->validateNodeNotEmpty($name, 'Название темы');

            $theme->setId($id)->setName($name);
            $this->_disciplineManager->addTheme($theme, $discipline);

        } catch (Exception $exception){
            array_push($importResult->errors, $exception->getMessage());
            $importResult->failed++;
        }
    }

    function parseAndSaveTest($testData, ImportResultViewModel $importResult){
        try{
            $test = new Test();

            $id = $this->getTextBetweenTags($testData, 't-id', true);
            $this->validateNodeNotEmpty($id, 'Id теста');

            $discipline = $this->getTextBetweenTags($testData, 't-discipline', true);
            $this->validateNodeNotEmpty($discipline, "Id дисциплины, к которой принадлежит тест ".$id);

            $name = $this->getTextBetweenTags($testData, 't-name', true);
            $this->validateNodeNotEmpty($name, 'Название теста'.$id);

            $time = $this->getTextBetweenTags($testData, 't-time', true);
            $this->validateNodeNotEmpty($time, 'Длительность теста'.$id);

            $attempts = $this->getTextBetweenTags($testData, 't-attempts', true);
            $this->validateNodeNotEmpty($attempts, 'Количество попыток теста'.$id);

            $test->setId($id)->setSubject($name)->setTimeTotal($time)->setAttempts($attempts)->setIsActive(true);
            $test->setType(TestType::Control);

            $this->_testManager->create($test, [], $discipline);

        } catch (Exception $exception){
            array_push($importResult->errors, $exception->getMessage());
            $importResult->failed++;
        }
    }

    function parseAndSaveTestTheme($testThemeData, $importResult){
        try{
            $testThemeLink = new TestTheme();

            $testId = $this->getTextBetweenTags($testThemeData, 't-test', true);
            $themeId = $this->getTextBetweenTags($testThemeData, 't-theme', true);

            $test = $this->_testManager->getById($testId);
            $theme = $this->_disciplineManager->getTheme($themeId);

            if (!isset($test)){
                throw new Exception("Невозможно связать тест с темой! Тест не найден!");
            }
            if (!isset($theme)){
                throw new Exception("Невозможно связать тест с темой! Тема не найдена!");
            }

            $testThemeLink->setTest($test)->setTheme($theme);
            $this->_unitOfWork->testThemes()->create($testThemeLink);
            $this->_unitOfWork->commit();

        } catch (Exception $exception){
            array_push($importResult->errors, $exception->getMessage());
            $importResult->failed++;
        }
    }


    function parseAndSaveQuestion($questionData, ImportResultViewModel $importResult)
    {
        try {
            $question = new Question();
            $text = $this->getTextBetweenTags($questionData, 'q-text', true);
            $this->validateQuestionText($text);
            $question->setText($text);

            $themeId = $this->getTextBetweenTags($questionData, 'q-theme', true);
            $this->validateNodeNotEmpty($questionData, 'Тема');

            $image = $this->getTextBetweenTags($questionData, 'q-image', true);
            isset($image) && !empty($image) && strpos('.', $image)? $question->setImage($image) : null;

            $type = $this->getTextBetweenTags($questionData, 'q-type', true);
            $this->validateQuestionType($type, $text);
            $question->setType($type);

            $complexity = $this->getTextBetweenTags($questionData, 'q-complexity', true);
            $this->validateQuestionComplexity($complexity, $text);
            $question->setComplexity($complexity);

            $time = $this->getTextBetweenTags($questionData, 'q-time', true);
            $this->validateQuestionTime($time, $text);
            $question->setTime($time);

            $answers = $this->parseQuestionAnswers($questionData, $question->getText());
            $this->_questionManager->create($question, $themeId, $answers);

            $importResult->imported++;
        } catch (Exception $exception) {
            array_push($importResult->errors, $exception->getMessage());
            $importResult->failed++;
        }
    }

    /**
     * Парсинг ответов на вопрос теста.
     * @param $questionData - Данные вопроса в виде текста, прочитанного из файла.
     * @param $questionText - Текст самого вопроса. Используется чтобы хоть как-то идентифицировать вопрос,
     * в ответах котрого будет обнаружена ошибка, т.к. до импорта у вопросов ещё нет идентификаторов.
     * @return array|null - Возвращает массив ответов.
     */
    private function parseQuestionAnswers($questionData, $questionText){
        $answers = [];
        $answersData = $this->getTextBetweenTags($questionData, 'answer');

        foreach ($answersData as $answer){
            array_push($answers, $this->tryParseAnswer($answer, $questionText));
        }

        return empty($answers) ? null : $answers;
    }

    private function tryParseAnswer($answerData, $questionText){
        $answer = new Answer();

        $text = $this->getTextBetweenTags($answerData, 'a-text', true);
        $this->validateAnswerText($text, $questionText);
        $answer->setText($text);

        $right = $this->getTextBetweenTags($answerData, 'a-right', true);
        $this->validateAnswerRight($right, $questionText);
        $answer->setIsRight((bool)$right);

        return $answer;
    }

    private function validateQuestionText($questionText){
        $this->validateNodeNotEmpty($questionText, 'Текст вопроса');
    }

    private function validateQuestionType($questionType, $questionText){
        $this->validateNodeNotEmpty($questionType, 'Тип вопроса');
        switch ($questionType){
            case QuestionType::ClosedOneAnswer:
            case QuestionType::ClosedManyAnswers:
            case QuestionType::OpenOneString:
            case QuestionType::OpenManyStrings: break;

            default: $this->throwWrongValueException('Тип вопроса', $questionText, $questionType);
        }
    }

    private function validateQuestionComplexity($questionComplexity, $questionText){
        $this->validateNodeNotEmpty($questionComplexity, 'Сложность вопроса');
        switch ($questionComplexity){
            case QuestionComplexity::Low:
            case QuestionComplexity::Medium:
            case QuestionComplexity::High: break;

            default: $this->throwWrongValueException('Сложность вопроса', $questionText, $questionComplexity);
        }
    }

    private function validateQuestionTime($questionTime, $questionText){
        $this->validateNodeNotEmpty($questionTime, 'Время на ответ');
        $time = (int)$questionTime;

        if (!isset($time) || $time <= 0 || $time > 3600){
            $this->throwWrongValueException('Время на ответ', $questionText, $questionTime);
        }
    }

    private function validateAnswerText($answerText, $questionText){
        $this->validateNodeNotEmpty($answerText, 'Текст ответа');
    }

    private function validateAnswerRight($answerRight, $questionText){
        if ($answerRight != 0 && $answerRight != 1){
            $this->throwWrongValueException('Правильность ответа', $questionText, $answerRight);
        }
    }

    private function validateNodeNotEmpty($nodeContent, $nodeName){
        if (!isset($nodeContent) || empty($nodeContent)){
            throw new Exception('Ошибка разбора вопроса. Не указано поле ['.$nodeName.']!');
        }
    }

    private function throwWrongValueException($fieldName, $questionText, $value = null){
        $message = 'В вопросе "'.$this->cutQuestionText($questionText)
            .'" указано некорректное значение для поля ['.$fieldName.']';
        if (isset($value)){
            $message .= ": $value.";
        }

        throw new Exception($message);
    }
}
