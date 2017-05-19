<?php

use CodeQuestionEngine\CodeQuestionManager;

class CodeQuestionManagerTest extends TestCase
{


    private $codeQuestionManager;

    protected function setUp()
    {
        parent::setUp();

        $this->codeQuestionManager = CodeQuestionManager();
    }

    /**
     * Действия, которые будут выполнены после запуска теста.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }


}