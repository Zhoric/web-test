<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IRepository;

abstract class BaseRepository implements IRepository
{
    protected $em;
    protected $model;

    public function __construct(EntityManager $entityManager, $entityName)
    {
        $this->em = $entityManager;
        $this->model = $this->em->entity($entityName);
    }

    public function all($relatedEntityName = null)
    {
        if ($relatedEntityName == null){
            return $this->model->get()->all();
        } else {
            return $this->model->with($relatedEntityName)->get()->all();
        }
    }

    public function create($entity)
    {
        return $this->em->insert($entity);
    }

    public function update($entity)
    {
        $this->em->update($entity);
    }

    public function delete($entity)
    {
        return $this->em->delete($entity);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function where($fieldName, $expression, $value)
    {
        return $this->model->where($fieldName, $expression, $value)->get();
    }

    public function findWith($id, $relatedEntityName)
    {
        return $this->model
            ->where('id', '=', $id)
            ->with($relatedEntityName)
            ->get()->first();
    }

    public function whereWith($fieldName, $expression, $value, $relatedEntityName, $takeFirst = false)
    {
        $query =  $this->model
            ->where($fieldName, $expression, $value)
            ->with($relatedEntityName);

        if ($takeFirst) {
            return $query->get()->first();
        } else{
            return $query->get();
        }
    }
}