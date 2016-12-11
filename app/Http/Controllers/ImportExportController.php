<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Managers\ImportExportManager;
use Managers\UISettingsCacheManager;


class ImportExportController extends Controller{

    private $_importExportManager;

    public function __construct(ImportExportManager $importExportManager)
    {
        $this->_importExportManager = $importExportManager;
    }

    public function exportQuestions($themeId){
        try{
            $result = $this->_importExportManager->exportQuestions($themeId);
            return $this->successJSONResponse($result);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function importQuestions($themeId){
        try{
            $result = $this->_importExportManager->importQuestions($themeId, null);
            return $this->successJSONResponse($result);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


}