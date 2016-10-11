<?php

namespace Repositories;

use App\Entities;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Repositories\Interfaces\IRepository;

abstract class BaseRepository implements IRepository
{
    protected $em;
    protected $model;
    protected $repo;

    public function __construct(EntityManager $entityManager, $entityName)
    {
        $this->em = $entityManager;
        $this->model = $entityName;
        $this->repo = $this->em->getRepository($this->model);
    }

    public function all()
    {
        return $this->repo->findAll();
    }

    public function paginate($pageSize, $pageNum, QueryBuilder $query = null, $orderBy = null){
        $query = ($query == null) ? $this->repo->createQueryBuilder($this->model) : $query;
        $orderBy = ($orderBy == null) ? $this->model.'.id' : $orderBy;
        $query = $query->orderBy($this->model.'.'.$orderBy)
            ->setMaxResults($pageSize)
            ->setFirstResult($pageSize * ($pageNum - 1));
        return $query->getQuery()->getArrayResult();
    }

    public function create($entity)
    {
        return $this->em->persist($entity);
    }

    public function update($entity)
    {
        $this->em->merge($entity);
    }

    public function delete($entity)
    {
        return $this->em->remove($entity);
    }

    public function find($id)
    {
        return $this->em->find($this->model, $id);
    }

    /*
     * Примеры использования:
     * _userRepo->where('User.id = 5');
     * _userRepo->where("User.firstname like '%Алекс%'");
     */
    public function where($predicate)
    {
        $query = $this->repo->createQueryBuilder($this->model)->where($predicate);

        return $query->getQuery()->getArrayResult();
    }
}