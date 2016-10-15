<?php

namespace Managers;

use Answer;
use Repositories\UnitOfWork;
use Question;

class QuestionManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getByThemeAndTextPaginated($pageSize, $pageNum, $themeId, $text){
        return $this->_unitOfWork
            ->questions()
            ->getByThemeAndTextPaginated($pageSize, $pageNum, $themeId, $text);
    }

    public function create(Question $question, $themeId, array $answers = null){
        $theme = $this->_unitOfWork->themes()->find($themeId);
        $question->setTheme($theme);
        $this->_unitOfWork->questions()->create($question);
        $this->_unitOfWork->commit();

        foreach ($answers as $answer){
            $newAnswer = new Answer();
            $newAnswer->setText($answer['text']);
            $newAnswer->setIsRight($answer['isRight']);
            $newAnswer->setQuestion($question);

            $this->_unitOfWork->answers()->create($newAnswer);
        }

        $this->_unitOfWork->commit();
    }

    public function update(Question $question, $themeId, array $answers = null){
        $theme = $this->_unitOfWork->themes()->find($themeId);
        $question->setTheme($theme);
        $this->_unitOfWork->questions()->update($question);
        $this->_unitOfWork->commit();

        $question = $this->_unitOfWork->questions()->find($question->getId());

        $this->_unitOfWork->answers()->deleteQuestionAnswers($question->getId());

        foreach ($answers as $answer){
            $newAnswer = new Answer();
            $newAnswer->setText($answer['text']);
            $newAnswer->setIsRight($answer['isRight']);
            $newAnswer->setQuestion($question);

            $this->_unitOfWork->answers()->create($newAnswer);
        }

        $this->_unitOfWork->commit();
    }

}