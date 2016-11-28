<?php

namespace Managers;

use Answer;
use Exception;
use GivenAnswer;
use ParamsSet;
use Program;
use QuestionType;
use QuestionViewModel;
use Repositories\UnitOfWork;
use Question;

class QuestionManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getByParamsPaginated($pageSize, $pageNum,
                                               $themeId, $text, $type, $complexity){
        return $this->_unitOfWork
            ->questions()
            ->getByParamsPaginated($pageSize, $pageNum,
                $themeId, $text, $type, $complexity);
    }

    public function create(Question $question, $themeId,
                           array $answers = null, $program = null, $paramSets = null){
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

        if ($question->getType() === QuestionType::WithProgram){
            $this->addQuestionProgram($question, $program, $paramSets);
        }
    }

    public function update(Question $question, $themeId,
                           array $answers = null, $program = null, $paramSets = null){
        $theme = $this->_unitOfWork->themes()->find($themeId);
        $question->setTheme($theme);
        $this->_unitOfWork->questions()->update($question);
        $this->_unitOfWork->commit();

        $question = $this->_unitOfWork->questions()->find($question->getId());

        $this->_unitOfWork->answers()->deleteQuestionAnswers($question->getId());

        if (isset($answers)){
            foreach ($answers as $answer){
                $newAnswer = new Answer();
                $newAnswer->setText($answer['text']);
                $newAnswer->setIsRight($answer['isRight']);
                $newAnswer->setQuestion($question);

                $this->_unitOfWork->answers()->create($newAnswer);
            }
        }

        $this->_unitOfWork->commit();

        if ($question->getType() === QuestionType::WithProgram){
            $this->updateQuestionProgram($question, $program, $paramSets);
        }
    }

    public function delete($id){
        $question = $this->_unitOfWork->questions()->find($id);
        if ($question != null){
            $this->_unitOfWork->questions()->delete($question);
        }
        $this->_unitOfWork->commit();
    }

    /**
     * Получение вопроса с ответами.
     * @param $questionId
     * @return QuestionViewModel
     */
    public function getWithAnswers($questionId){
        $question = $this->_unitOfWork->questions()->find($questionId);
        $answers = $this->_unitOfWork->answers()->getByQuestion($questionId);

        $program = $this->_unitOfWork->programs()->getByQuestion($question->getId());

        if (isset($program)){
            $paramSets = $this->_unitOfWork->paramsSets()->getByProgram($program);
            return new QuestionViewModel($question, $answers, $program, $paramSets);
        } else {
            return new QuestionViewModel($question, $answers);
        }
    }

    public function getById($id){
        return $this->_unitOfWork->questions()->find($id);
    }

    public function createQuestionAnswer(GivenAnswer $givenAnswer){
        $this->_unitOfWork->givenAnswers()->create($givenAnswer);
        $this->_unitOfWork->commit();
    }

    /**
     * Добавление программы и тестовых наборов параметров к вопросу.
     * @param $question
     * @param $programText
     * @param $paramSets
     * @throws Exception
     */
    private function addQuestionProgram($question, $programText, $paramSets){
        $program = new Program();
        $program->setQuestion($question);
        $program->setTemplate($programText);
        //TODO[NZ]: Добавить перечисление языков и выбор языка при добавлении вопроса.
        $program->setLang(1);
        $this->_unitOfWork->programs()->create($program);
        $this->_unitOfWork->commit();

        if (!isset($paramSets)){
            throw new Exception('Не указаны тестовые наборы данных для программы!');
        }

        foreach ($paramSets as $paramSet){
            $newParamSet = new ParamsSet();
            $newParamSet->setProgram($program);
            $newParamSet->setInput($paramSet['input']);
            $newParamSet->setExpectedOutput($paramSet['expectedOutput']);

            $this->_unitOfWork->paramsSets()->create($newParamSet);
        }

        $this->_unitOfWork->commit();
    }

    /**
     * Обновление программы и тестовых наборов параметров к вопросу.
     * @param $question
     * @param $programText
     * @param $paramSets
     * @throws Exception
     */
    private function updateQuestionProgram($question, $programText, $paramSets){
        $program = $this->_unitOfWork->programs()->getByQuestion($question->getId());
        if (!isset($program)){
            throw new Exception('Невозможно обновить данные вопроса. Программа не найдена!');
        }
        $program->setTemplate($programText);
        $this->_unitOfWork->programs()->update($program);
        $this->_unitOfWork->commit();

        if (!isset($paramSets)){
            throw new Exception('Не указаны тестовые наборы данных для программы!');
        }

        $this->_unitOfWork->paramsSets()->deleteProgramParams($program->getId());

        foreach ($paramSets as $paramSet){
            $newParamSet = new ParamsSet();
            $newParamSet->setProgram($program);
            $newParamSet->setInput($paramSet['input']);
            $newParamSet->setExpectedOutput($paramSet['expectedOutput']);

            $this->_unitOfWork->paramsSets()->create($newParamSet);
        }

        $this->_unitOfWork->commit();
    }

}