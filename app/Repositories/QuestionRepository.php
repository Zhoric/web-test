<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;
use Repositories\Interfaces\IQuestionRepository;

class QuestionRepository extends BaseRepository implements IQuestionRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Question');
    }

    public function addAnswer($answer)
    {
        $this->em->insert($answer);
    }

    public function deleteAnswer($answer)
    {
        $this->em->delete($answer);
    }
}