<?php

namespace Managers;

use QuestionViewModel;
use Repositories\UnitOfWork;
use Test;

class TestManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function create(Test $test, array $themeIds){
        $this->_unitOfWork->tests()->create($test);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->tests()->setTestThemes($test->getId(), $themeIds);
        $this->_unitOfWork->commit();
    }

    public function update(Test $test, array $themeIds){
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

    /**
     * @param $questionId
     * @param bool $showWhichRight - показывать ли правильность ответов.
     * @return QuestionViewModel
     */
    public function getQuestionWithAnswers($questionId, $showWhichRight = true){
        $question = $this->_unitOfWork->questions()->find($questionId);
        $answers = $this->_unitOfWork->answers()->getByQuestion($questionId);

        if ($showWhichRight == false){
            for($i = 0; $i < count($answers); $i++){
                $answers[$i]->setIsRight(null);
            }
        }

        return new QuestionViewModel($question, $answers);
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
}