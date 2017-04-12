<?php

namespace App\Http\Controllers;

use Exception;
use Helpers\FileHelper;
use Managers\ImportExportManager;
use Illuminate\Http\Request;
use Managers\OldDbImportManager;


class ImportExportController extends Controller{

    private $_importExportManager;
    private $_oldDbImportManager;

    public function __construct(ImportExportManager $importExportManager,
                                OldDbImportManager $oldDbImportManager)
    {
        $this->_importExportManager = $importExportManager;
        $this->_oldDbImportManager = $oldDbImportManager;
    }

    public function exportQuestions($themeId){
        try{
            $exportFilePath = $this->_importExportManager->exportQuestions($themeId);

            return response()
                ->download($exportFilePath, 'questions_import_theme_'.$themeId)
                ->deleteFileAfterSend(true);

        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function importQuestions(Request $request){
        try{
            $file = $request->json('file');
            $themeId = $request->json('themeId');

            $importFilePath = ImportExportManager::$importPath.ImportExportManager::$importFileName;
            FileHelper::delete($importFilePath);

            $result = $this->_importExportManager->importQuestions($themeId, $file);
            return $this->successJSONResponse($result);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function importAll(Request $request){
        $result = $this->_oldDbImportManager->importQuestions();
        dd($result);
    }


}