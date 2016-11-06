<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use TestMarkType;

class TestMarkTypeRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestMarkType::class);
    }

    /*
     * Связывание типа оценки (например: "Оценка за Тест №1", "Лабораторная работа №1")
     * с конкретным тестом
     */
    public function setTestMarkType(TestMarkType $testMarkType){
        $qb = $this->repo->createQueryBuilder('tmt');
        $deleteQuery = $qb->delete()
            ->where('tmt.markType = :markType')
            ->setParameter('markType', $testMarkType->getMarkType()->getId())
            ->getQuery();

        $deleteQuery->execute();

        if ($testMarkType->getTest() != null){
            $this->create($testMarkType);
        }
    }
}