<?php

use Ixudra\Curl\Facades\Curl;
class CodeQuestionManagerProxy
{

    public function runQuestionProgram($code,$program, $testResult,$question)
    {
        $contract = new RunProgramDataContract();
        $contract->setCode($code);
        $contract->setProgramId($program->getId());
        $contract->setTestResultId($testResult->getId());
        $contract->setQuestionId($question->getId());
        $contract->setLanguage($program->getLang());
        $contract->setUserId(Auth::user()->getId());

        $baseUrl = ConnectionConfigSettings::$BASE_URL;
        $action = ConnectionConfigSettings::$RUN_PROGRAM_URL;

        $response = Curl::to($baseUrl.'/'.$action)
            ->withData( $contract->jsonSerialize())
            ->post();

        return $response;

    }

}