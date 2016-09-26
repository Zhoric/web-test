<?php

namespace Repositories;

use Repositories\Interfaces;
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

    public function all()
    {
        return $this->model->get()->all();
    }

    public function create($entity)
    {
        return $this->em->insert($entity);
    }

    public function update($entity)
    {
        return $this->em->update($entity);
    }

    public function delete($id)
    {
        return $this->em->delete($id);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function where($fieldName, $expression, $value)
    {
        return $this->model->where($fieldName, $expression, $value)->get();
    }
}