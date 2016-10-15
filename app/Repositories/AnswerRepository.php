<?php

namespace Repositories;

use Answer;
use Doctrine\ORM\EntityManager;

class AnswerRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Answer::class);
    }

    public function getByQuestion($questionId){
        return $this->repo->findBy(['question' => $questionId]);
    }

    public function deleteQuestionAnswers($questionId){
        $qb = $this->repo->createQueryBuilder('a');
        $deleteQuery = $qb->delete()
            ->where('a.question = :question')
            ->setParameter('question', $questionId)
            ->getQuery();

        $deleteQuery->execute();
    }
}