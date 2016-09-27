<?php

namespace Repositories\Interfaces;

interface IQuestionRepository extends IRepository
{
    public function addAnswer($answer);

    public function deleteAnswer($answer);
}