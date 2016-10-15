<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use PaginationResult;
use Question;

class QuestionRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Question::class);
    }

    public function getByThemeAndTextPaginated($pageSize, $pageNum, $themeId, $text = null){
        $qb = $this->repo->createQueryBuilder('q');
        $query = $qb;

        $query = $query->where('q.theme = :themeId')
            ->setParameter('themeId', $themeId);

        if ($text != null && $text != ''){
            $query = $query->where('q.text LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        $countQuery = clone $query;
        $data =  $this->paginate($pageSize, $pageNum, $query, 'q.id');

        $count = $countQuery->select(
            $qb->expr()
                ->count('q.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginationResult($data, $count);
    }
}