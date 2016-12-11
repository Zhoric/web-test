<?php

namespace Managers;

use Answer;
use Exception;
use ExportResultViewModel;
use Helpers\DateHelper;
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
    private static $importPath = 'import/';

    private $_questionManager;

    public function __construct(QuestionManager $questionManager)
    {
        $this->_questionManager = $questionManager;
    }

    public function importQuestions($themeId, $file)
    {
        $fileContent = file_get_contents(self::$importPath . "test.txt");
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

    /**
     * Экспорт всех вопросов из заданной темы в текстовый файл.
     * @param $themeId
     * @return ExportResultViewModel
     */
    public function exportQuestions($themeId)
    {
        $filePath = self::$importPath . $this->getFileName($themeId);
        $exportFile = fopen($filePath, 'w');
        $exportResult = new ExportResultViewModel();
        $exportContent = "";

        $questions = $this->_questionManager->getByTheme($themeId);
        $exportResult->totalRows = count($questions);

        foreach ($questions as $question) {
            $questionData = $this->getQuestionDataString($question, $exportResult);
            $exportContent .= $questionData;
        }

        fwrite($exportFile, $exportContent);
        fclose($exportFile);
        return $exportResult;
    }

    private function getFileName($themeId)
    {
        $currentDate = DateHelper::getCurrentDateTimeString();
        $fileName = "Theme$themeId" . '_' . "$currentDate.txt";

        return $fileName;
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
     * @param $questionData
     * @param $importResult
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

            default: $this->throwWrongValueException('Тип вопроса', $questionText);
        }
    }

    private function validateQuestionComplexity($questionComplexity, $questionText){
        $this->validateNodeNotEmpty($questionComplexity, 'Сложность вопроса');
        switch ($questionComplexity){
            case QuestionComplexity::Low:
            case QuestionComplexity::Medium:
            case QuestionComplexity::High: break;

            default: $this->throwWrongValueException('Сложность вопроса', $questionText);
        }
    }

    private function validateQuestionTime($questionTime, $questionText){
        $this->validateNodeNotEmpty($questionTime, 'Время на ответ');
        $time = (int)$questionTime;

        if (!isset($time) || $time <= 0 || $time > 3600){
            $this->throwWrongValueException('Время на ответ', $questionText);
        }
    }

    private function validateAnswerText($answerText, $questionText){
        $this->validateNodeNotEmpty($answerText, 'Текст ответа');
    }

    private function validateAnswerRight($answerRight, $questionText){
        if ($answerRight != 0 && $answerRight != 1){
            $this->throwWrongValueException('Правильность ответа', $questionText);
        }
    }

    private function validateNodeNotEmpty($nodeContent, $nodeName){
        if (!isset($nodeContent) || empty($nodeContent)){
            throw new Exception('Ошибка разбора вопроса. Не указано поле ['.$nodeName.']!');
        }
    }

    private function throwWrongValueException($fieldName, $questionText){
        throw new Exception('В вопросе "'.$this->cutQuestionText($questionText)
            .'" указано некорректное значение для поля ['.$fieldName.']');
    }
}
