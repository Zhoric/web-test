<?php

namespace App\Http\Controllers;

use Exception;
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
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $text = $request->query('name');
            $themeId = $request->query('theme');
            $type = $request->query('type');
            $complexity = $request->query('complexity');

            $paginationResult = $this->_questionManager
                ->getByParamsPaginated($pageSize, $pageNum,
                    $themeId, $text, $type, $complexity);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /*
     *   Добавление вопроса вместе с ответами
     *   Пример валидного JSON-запроса:
     *   {"question" : {"type": 1, "text": "Текст вопроса?", "complexity": 2, "time": 30},
     *    "theme" : 2,
     *    "answers" : [{"text":"Правильный ответ","isRight":true},
     *                 {"text":"Неправильный ответ","isRight":false}],
     *    "file" : "Содержимое файла в base64",
     *    "fileType" : "Тип файла",
     *    "program" : "Текст программы",
     *    "paramSets" : [{"input":"Входные параметры1", "expectedOutput":"Выходной параметр1"},
     *      {"input":"Входные параметры2", "expectedOutput":"Выходной параметр2"}],
     *    }
     */
    public function create(Request $request){
        try{
            $questionData = $request->json('question');
            $answers = (array) $request->json('answers');
            $themeId = $request->json('theme');
            $file = $request->json('file');
            $fileType = $request->json('fileType');
            $program = $request->json('program');
            $paramSets = (array) $request->json('paramSets');

            $question = new Question();
            $question->fillFromJson($questionData);

            if ($file != null){
                $filePath = FileHelper::save($file, $fileType);
                $question->setImage($filePath);
            }
            $this->_questionManager->create($question,$themeId,$answers,$program,$paramSets);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


    /*
     * При обновлении вопроса с файлом изображения,
     * если в ходе редактирования вопроса изображение не изменялось,
     * в fileType следует записать значение 'OLD'
     * Если при редактировании изображение удалено, то поля file и fileType должны быть null.
     */
    public function update(Request $request){
        try{
            $questionData = $request->json('question');
            $answers = $request->json('answers');
            $themeId = $request->json('theme');
            $file = $request->json('file');
            $fileType = $request->json('fileType');
            $program = $request->json('program');
            $paramSets = (array) $request->json('paramSets');

            $question = new Question();
            $question->fillFromJson($questionData);

            $oldQuestion = $this->_questionManager->getById($question->getId());
            $oldImage = $oldQuestion->getImage();
            if ($oldImage != null && $fileType != 'OLD'){
                FileHelper::delete($oldImage);
            }

            if ($file != null){
                $filePath = FileHelper::save($file, $fileType);
                $question->setImage($filePath);
            }

            $this->_questionManager->update($question,$themeId, $answers, $program, $paramSets);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($id){
        try{
            $this->_questionManager->delete($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /*
     *   Получение вопроса с ответами по id
     */
    public function get($id){
        try{
            $question =  $this->_questionManager->getWithAnswers($id);
            return $this->successJSONResponse($question);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}