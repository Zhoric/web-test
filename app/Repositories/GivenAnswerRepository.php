<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use GivenAnswer;
use Question;
use QuestionType;

class GivenAnswerRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, GivenAnswer::class);
    }

    public function getByTestResult($testResultId){
        return $this->repo->findBy(['testResult' => $testResultId]);
    }

    public function getBadAnswersForTestResult($testResultId, $closedAnswerMinGoodValue, $openAnswerMinGoodValue){
        $openQuestionType = QuestionType::OpenManyStrings;

        $qb = $this->repo->createQueryBuilder('ga');
        $query = $qb->join(Question::class, 'q', Join::WITH, 'ga.question = q.id')
            ->where("ga.testResult = $testResultId AND q.type = $openQuestionType
             AND ga.rightPercentage < $closedAnswerMinGoodValue")
            ->where("ga.testResult = $testResultId AND q.type <> $openQuestionType
             AND ga.rightPercentage < $openAnswerMinGoodValue")
            ->getQuery();

        return $query->execute();
    }

    public function getIdsByTestResult($testResultId){
        $qb = $this->repo->createQueryBuilder('ga');
        $query = $qb
            ->join(Question::class, 'q', Join::WITH, "ga.testResult = $testResultId AND ga.question = q.id")
            ->select("q.id")
            ->getQuery();

        return $query->execute();
    }


}