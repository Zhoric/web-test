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

    public function getByParamsPaginated($pageSize, $pageNum, $themeId,
                                               $text, $type, $complexity){
        $qb = $this->repo->createQueryBuilder('q');
        $query = $qb;

        $query = $query->andWhere('q.theme = :themeId')
            ->setParameter('themeId', $themeId);

        if ($text != null && $text != ''){
            $query = $query->andWhere('q.text LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        if ($type != null && $type != ''){
            $query = $query->andWhere('q.type = :type')
                ->setParameter('type', $type);
        }

        if ($complexity != null && $complexity != ''){
            $query = $query->andWhere('q.complexity = :complexity')
                ->setParameter('complexity', $complexity);
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