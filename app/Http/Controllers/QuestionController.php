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

    /*
     *  Постраничное получение всех вопросов по теме и тексту
     *  Текст - необязательный параметр
     */
    public function getByThemeAndTextPaginated(Request $request){
        $pageNum =  $request->query('page');
        $pageSize = $request->query('pageSize');
        $text = $request->query('name');
        $themeId = $request->query('theme');

        $paginationResult = $this->_questionManager
            ->getByThemeAndTextPaginated($pageSize, $pageNum, $themeId, $text);

        return json_encode($paginationResult);
    }

    /*
     *   Добавление вопроса вместе с ответами
     *   Пример валидного JSON-запроса:
     *   {"question" : {"type": 1, "text": "Текст вопроса?", "complexity": 2, "time": 30},
     *    "theme" : 2,
     *    "answers" : [{"text":"Правильный ответ","isRight":true},
     *                 {"text":"Неправильный ответ","isRight":false}]
     *    }
     */
    public function create(Request $request){
        $questionData = $request->json('question');
        $answers = (array) $request->json('answers');
        $themeId = $request->json('theme');

        $question = new Question();
        $question->fillFromJson($questionData);

        $this->_questionManager->create($question,$themeId, $answers);
    }

    /*
     *   Обновление вопроса вместе с ответами
     *   Пример валидного JSON-запроса:
     *   {"question" : { "id":19,"type": 1, "text": "Текст вопроса?", "complexity": 2, "time": 30},
     *    "theme" : 2,
     *    "answers" : [{"text":"Правильный ответ","isRight":true},
     *                 {"text":"Неправильный ответ1","isRight":false},
     *                 {"text":"Неправильный ответ2","isRight":false}]
     *    }
     */
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