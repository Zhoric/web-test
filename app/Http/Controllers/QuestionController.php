<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Managers\QuestionManager;
use Question;


class QuestionController extends Controller
{
    private $_questionManager;

    public function __construct(QuestionManager $questionManager)
    {
        $this->_questionManager = $questionManager;
    }

    public function getByThemeAndTextPaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $text = $request->query('name');
        $themeId = $request->query('theme');

        $paginationResult = $this->_questionManager
            ->getByThemeAndTextPaginated($pageSize, $pageNum, $themeId, $text);

        return json_encode($paginationResult);
    }

    public function create(Request $request){
        $questionData = $request->json('question');
        $answers = (array) $request->json('answers');
        $themeId = $request->json('theme');

        $question = new Question();
        $question->fillFromJson($questionData);

        $this->_questionManager->create($question,$themeId, $answers);
    }

    public function update(Request $request){
        $questionData = $request->json('question');
        $answers = $request->json('answers');
        $themeId = $request->json('theme');

        $question = new Question();
        $question->fillFromJson($questionData);

        $this->_questionManager->update($question,$themeId, $answers);
    }

    public function delete($id){
        $this->delete($id);
    }
}