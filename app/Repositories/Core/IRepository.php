<?php

namespace Repositories\Interfaces;

interface IRepository
{
    public function all();

    public function create($entity);

    public function update($entity);

    public function delete($id);

    public function find($id);

    public function where($fieldName, $expression, $value);
}