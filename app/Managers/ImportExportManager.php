<?php

namespace Managers;

use Answer;
use Exception;
use ExportResultViewModel;
use Helpers\DateHelper;
use Helpers\FileHelper;
use ImportResultViewModel;
use Question;
use QuestionComplexity;
use QuestionType;

class ImportExportManager
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
     * Имя временного файла, хранящего данные для импорта вопросов.
     * Временного, так как процесс импорта состоит из следующих шагов:
     * 1. Загрузка временного файла через интерфейс пользователя.
     * 2. Обработка файла и сохранение вопросов в базу.
     * 3. Удаление временного файла.
     * @var string
     */
    public static $importFileName = 'import.xml';

    /**
     * Имя временного файла, хранящего данные экспортированных вопросов.
     * $questionType
     * @var string
     */
    private static $exportFileName = 'export.xml';

    /**
     * Тип файлов для импорта вопросов.
     * @var string
     */
    public static $importFileType = 'xml';

    private $_questionManager;

    public function __construct(QuestionManager $questionManager)
    {
        $this->_questionManager = $questionManager;
    }

    public function importQuestions($themeId, $file)
    {
        try{
            $importFilePath = self::$importPath.self::$importFileName;
            FileHelper::save($file, self::$importFileType, $importFilePath);

            $fileContent = file_get_contents($importFilePath);
            $importResult = new ImportResultViewModel();

            $questions = $this->getTextBetweenTags($fileContent, 'question');
            if (!isset($questions) || count($questions) == 0) {
                throw new Exception('Ошибка импорта! Не удаётся найти вопросы теста в указанном файле!');
            }

            $importResult->totalRows = count($questions);
            foreach ($questions as $question) {
                $this->tryParseAndSaveQuestion($question, $importResult, $themeId);
            }
            return $importResult;
        }
        finally {
            FileHelper::delete($importFilePath);
        }
    }

    /**
     * Экспорт всех вопросов из заданной темы в текстовый файл.
     * @param $themeId
     * @return string
     * @throws Exception
     */
    public function exportQuestions($themeId)
    {
        try{
            $filePath = self::$importPath . self::$exportFileName;
            FileHelper::delete($filePath);
            $exportFile = fopen($filePath, 'w');
            $exportResult = new ExportResultViewModel();
            $exportContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL;
            $exportContent .= "<questions-list>".PHP_EOL;


            $questions = $this->_questionManager->getByTheme($themeId);
            $exportResult->totalRows = count($questions);

            foreach ($questions as $question) {
                $questionData = $this->getQuestionDataString($question, $exportResult);
                $exportContent .= $questionData;
            }

            $exportContent .= "</questions-list>".PHP_EOL;

            fwrite($exportFile, $exportContent);

            if ($exportResult->exported > 0){
                return $filePath;
            } else {
                throw new Exception('Ни один из вопросов данной темы не может быть экспортирован.');
            }

        } finally {
            fclose($exportFile);
        }

    }

    /**
     * Получение данных вопроса в виде строки.
     * @param Question $question
     * @param ExportResultViewModel $exportResult
     * @return string
     */
    private function getQuestionDataString(Question $question, ExportResultViewModel $exportResult)
    {
        try {

            if ($question->getType() === QuestionType::WithProgram) {
                throw new Exception('Экспорт вопросов с программой не поддерживается! Вопрос "'
                    . $this->cutQuestionText($question->getText()) . '" будет проигнорирован.');
            }

            $questionData = "<question>" . PHP_EOL;
            $questionData .= "\t<q-type>" . $question->getType() . "</q-type>" . PHP_EOL;
            $questionData .= "\t<q-complexity>" . $question->getComplexity() . "</q-complexity>" . PHP_EOL;
            $questionData .= "\t<q-time>" . $question->getTime() . "</q-time>" . PHP_EOL;
            $questionData .= "\t<q-text>" . $question->getText() . "</q-text>" . PHP_EOL;

            $answers = $this->_questionManager->getQuestionAnswers($question->getId());
            foreach ($answers as $answer) {
                $questionData .= $this->getAnswerDataString($answer, $exportResult);
            }
            $questionData .= "</question>" . PHP_EOL;

            $exportResult->exported++;
            return $questionData;

        } catch (Exception $exception) {
            array_push($exportResult->errors, $exception->getMessage());
            $exportResult->failed++;
        }
    }

    /**
     * Получение данных ответа в виде строки.
     * @param Answer $answer
     * @param ExportResultViewModel $exportResult
     * @return string
     */
    private function getAnswerDataString(Answer $answer, ExportResultViewModel $exportResult)
    {
        $answerData = "\t<answer>" . PHP_EOL;
        $answerData .= "\t\t<a-text>" . $answer->getText() . "</a-text>" . PHP_EOL;
        $answerData .= "\t\t<a-right>" . (int)$answer->getIsRight() . "</a-right>" . PHP_EOL;
        $answerData .= "\t</answer>" . PHP_EOL;

        return $answerData;
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

    /**
     * Парсинг и сохранение вопроса в БД.
     * @param $questionData - Данные о вопросе теста, прочитанные из файла.
     * @param $importResult - Результат импорта. Передаётся в метод для агрегации ошибок импорта.
     */
    function tryParseAndSaveQuestion($questionData, ImportResultViewModel $importResult, $themeId)
    {
        try {
            $question = new Question();
            $text = $this->getTextBetweenTags($questionData, 'q-text', true);
            $this->validateQuestionText($text);
            $question->setText($text);

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
