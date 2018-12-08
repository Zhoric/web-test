<?php

namespace Managers;

use QuestionType;
use QuestionViewModel;
use Repositories\UnitOfWork;
use Test;
use TestInfoViewModel;
use ThemeViewModel;

class TestManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function create(Test $test, array $themeIds, $disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
        if (!isset($discipline)){
            throw new \Exception('Дисциплина не найдена!');
        }

        $test->setDiscipline($discipline);

        $this->_unitOfWork->tests()->create($test);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->tests()->setTestThemes($test->getId(), $themeIds);
        $this->_unitOfWork->commit();
    }

    public function update(Test $test, array $themeIds, $disciplineId){
        $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
        $test->setDiscipline($discipline);

        $this->_unitOfWork->tests()->update($test);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->tests()->setTestThemes($test->getId(), $themeIds);
        $this->_unitOfWork->commit();
    }

    public function delete($id){
        $test = $this->_unitOfWork->tests()->find($id);

        if ($test != null) {
            $this->_unitOfWork->tests()->delete($test);
            $this->_unitOfWork->commit();
        }
    }

    public function getTestsByNameAndDisciplinePaginated(
        $pageNum, $pageSize, $name, $disciplineId, $isActive)
    {
        return $this->_unitOfWork->tests()
            ->getByNameAndDisciplinePaginated($pageSize, $pageNum, $disciplineId, $name, $isActive);
    }
    
    /**
     * @param $id
     * @return Test
     */
    public function getById($id){
        return $this->_unitOfWork->tests()->find($id);
    }

    public function getNotAnsweredQuestionsByTest($testId, $answeredIds, $timeLeft){
        return $this->_unitOfWork->questions()
            ->getNotAnsweredQuestionsByTest($testId, $answeredIds, $timeLeft);
    }

    public function getQuestionsByTest($testId){
        return $this->_unitOfWork->questions()->getByTest($testId);
    }

    /**
     * @param $questionId
     * @param bool $showWhichRight - показывать ли правильность ответов.
     * @return QuestionViewModel
     */
    public function getQuestionWithAnswers($questionId, $showWhichRight = true){
        $question = $this->_unitOfWork->questions()->find($questionId);
        $answers = $this->_unitOfWork->answers()->getByQuestion($questionId);
        shuffle($answers);

        //Не отправляем студенту информацию о правильности ответов
        if ($showWhichRight == false){
            $answersForStudents = [];
            for($i = 0; $i < count($answers); $i++){
                $answer = new \Answer();
                $answer->setId($answers[$i]->getId());
                $answer->setQuestion($answers[$i]->getQuestion());
                $answer->setText($answers[$i]->getText());
                array_push($answersForStudents, $answer);
            }
            $answers = $answersForStudents;
        }

        //Если тест открытый с однострочным ответом - не отправляем студенту варианты ответов
        if ($question->getType() == QuestionType::OpenOneString
            || $question->getType() == QuestionType::OpenManyStrings){
            $answers = null;
        }

        //Достаём программный код из вопроса, если он есть.
        $program = $this->_unitOfWork->programs()->getByQuestion($question->getId());

        if (isset($program)){
            $paramSets = $this->_unitOfWork->paramsSets()->getByProgram($program);
            return new QuestionViewModel($question, $answers, $program, $paramSets);
        } else {
            return new QuestionViewModel($question, $answers);
        }
    }

    /**
     * Получение количества использованных попыток прохождения теста.
     * @param $testId
     * @param $userId
     * @return mixed
     */
    public function getTestAttemptsUsedCount($testId, $userId){
        return $this->_unitOfWork->testResults()->getLastAttemptNumber($testId, $userId);
    }

    /**
     * Получение тестов по заданной дисциплине, которые может видеть текущий студент.
     * @param $userId
     * @param $disciplineId
     * @return mixed
     */
    public function getTestsByUserAndDiscipline($userId, $disciplineId){
        $testsInfo = [];
        $tests =  $this->_unitOfWork->tests()->getByUserAndDiscipline($userId, $disciplineId);
        foreach ($tests as $test){
            $testId = $test->getId();
            $lastTestResult = $this->_unitOfWork->testResults()->getLastForUser($userId, $testId);
            $extraAttempts = $this->_unitOfWork->extraAttempts()->findByTestAndUser($testId, $userId);

            $lastMark = $lastTestResult != null ? $lastTestResult->getMark() : null;
            $lastAttemptNumber = $lastTestResult != null ? $lastTestResult->getAttempt() : 0;
            $extraAttemptsCount = $extraAttempts != null ? $extraAttempts->getCount() : 0;
            $attemptsAllowed = $test->getAttempts();

            $attemptsLeft = $attemptsAllowed + $extraAttemptsCount - $lastAttemptNumber;

            $testInfo = new TestInfoViewModel();
            $testInfo->setTest($test);
            $testInfo->setLastMark($lastMark);
            $testInfo->setAttemptsLeft($attemptsLeft);
            $testInfo->setAttemptsMade($lastAttemptNumber);

            array_push($testsInfo, $testInfo);
        }

        return $testsInfo;
    }

    public function getThemesOfTest($testId) {
        $themes = $this->_unitOfWork
            ->themes()
            ->getByTest($testId);
        $data = [];
        foreach ($themes as $theme) {
            $questions = $this->_unitOfWork
                ->questions()
                ->getByTheme($theme->getId());
            $data[] = new ThemeViewModel(
                $theme->getId(),
                $theme->getName(),
                $theme->getDiscipline()->getId(),
                count($questions),
                $this->getTotalTime($questions)
            );
        }
        return $data;
    }

    private function getTotalTime($questions) {
        $time = 0;
        foreach ($questions as $question)
            $time += $question->getTime();
        return $time;
    }
}