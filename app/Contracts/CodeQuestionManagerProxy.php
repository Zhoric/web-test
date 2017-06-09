<?php

use Ixudra\Curl\Facades\Curl;

class CodeQuestionManagerProxy
{

    public function runQuestionProgram($code,$program,$paramSets, $testResult,$question)
    {
        $contract = new RunProgramDataContract();

        $contract->setCode($code);
        $contract->setProgramId($program->getId());
        $contract->setLanguage($program->getLang());
        $contract->setTimeLimit($program->getTimeLimit());
        $contract->setMemoryLimit($program->getMemoryLimit());
        $contract->setParamSets($paramSets);

        $contract->setTestResultId($testResult->getId());
        $contract->setQuestionId($question->getId());

        $user = Auth::user();
        $f = $user->getLastname();
        $i = $user->getFirstname();
        $o = $user->getPatronymic();
        $fio = $f."_".$i."_".$o;
        $contract->setFio($fio);



        $baseUrl = ConnectionConfigSettings::$BASE_URL;
        $action = ConnectionConfigSettings::$RUN_QUESTION_PROGRAM_URL;
	

        $response = Curl::to($baseUrl.'/'.$action)
            ->withData( $contract->jsonSerialize())
            ->post();



        return $response;

    }


    public function runProgram($code,$language,$timeLimit,$memoryLimit,$paramSets){

        $contract =  new RunProgramDataContract();
        $contract->setCode($code);
        $contract->setParamSets($paramSets);
        $contract->setLanguage($language);
        $contract->setTimeLimit($timeLimit);
        $contract->setMemoryLimit($memoryLimit);

        $user = Auth::user();
        $f = $user->getLastname();
        $i = $user->getFirstname();
        $o = $user->getPatronymic();
        $fio = $f."_".$i."_".$o;
        $contract->setFio($fio);


        $baseUrl = ConnectionConfigSettings::$BASE_URL;
        $action = ConnectionConfigSettings::$RUN_PROGRAM_URL;

        $response = Curl::to($baseUrl.'/'.$action)
            ->withData( $contract->jsonSerialize())
            ->post();

        return $response;

    }

}
