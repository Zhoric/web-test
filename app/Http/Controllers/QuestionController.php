<?php

namespace App\Http\Controllers;

use Helpers\FileHelper;
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
        $type = $request->query('type');
        $complexity = $request->query('complexity');

        $paginationResult = $this->_questionManager
            ->getByParamsPaginated($pageSize, $pageNum,
                $themeId, $text, $type, $complexity);

        return json_encode($paginationResult);
    }

    /*
     *   Добавление вопроса вместе с ответами
     *   Пример валидного JSON-запроса:
     *   {"question" : {"type": 1, "text": "Текст вопроса?", "complexity": 2, "time": 30},
     *    "theme" : 2,
     *    "answers" : [{"text":"Правильный ответ","isRight":true},
     *                 {"text":"Неправильный ответ","isRight":false}]
     *    "file" : "Содержимое файла в base64",
     *    "fileType" : "Тип файла"
     *    }
     */
    public function create(Request $request){
        $questionData = $request->json('question');
        $answers = (array) $request->json('answers');
        $themeId = $request->json('theme');
        $file = $request->json('file');
        $fileType = $request->json('fileType');

        $question = new Question();
        $question->fillFromJson($questionData);

        if ($file != null){
            $filePath = FileHelper::save($file, $fileType);
            $question->setImage($filePath);
        }

        $this->_questionManager->create($question,$themeId, $answers);
    }

    //TODO: Реализовать удаление изображения при обновлении.
    public function update(Request $request){
        $questionData = $request->json('question');
        $answers = $request->json('answers');
        $themeId = $request->json('theme');
        $file = $request->json('file');
        $fileType = $request->json('fileType');

        $question = new Question();
        $question->fillFromJson($questionData);

        if ($file != null){
            $filePath = FileHelper::save($file, $fileType);
            $question->setImage($filePath);
        } else {
            $oldQuestion = $this->_questionManager->getById($question->getId());
            $oldImage = $oldQuestion->getImage();
            if ($oldImage != null && !emptyString($oldImage)){
                $question->setImage($oldImage);
            }
        }

        $this->_questionManager->update($question,$themeId, $answers);
    }

    public function delete($id){
        $this->_questionManager->delete($id);
    }

    /*
     *   Получение вопроса с ответами по id
     */
    public function get($id){
        $question =  $this->_questionManager->getWithAnswers($id);
        return json_encode($question);
    }
}