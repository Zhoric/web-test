<?php

namespace Managers;

use Repositories\UnitOfWork;
use Test;

class TestManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function create(Test $test, array $themeIds){
        $this->_unitOfWork->tests()->create($test);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->tests()->setTestThemes($test->getId(), $themeIds);
        $this->_unitOfWork->commit();
    }

    public function update(Test $test, array $themeIds){
        $this->_unitOfWork->tests()->update($test);
        $this->_unitOfWork->commit();

        $this->_unitOfWork->tests()->setTestThemes($test->getId(), $themeIds);
        $this->_unitOfWork->commit();
    }

    public function delete($id){
        $test = $this->_unitOfWork->tests()->find($id);

        if ($test != null){
            $this->_unitOfWork->tests()->delete($test);
            $this->_unitOfWork->commit();
        }
    }
}