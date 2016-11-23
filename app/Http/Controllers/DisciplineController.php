<?php

namespace App\Http\Controllers;

use Discipline;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Managers\DisciplineManager;
use Managers\ScheduleManager;
use Theme;


class DisciplineController extends Controller
{
    private $_disciplineManager;
    private $_scheduleManager;

    public function __construct(DisciplineManager $disciplineManager,
                                ScheduleManager $scheduleManager)
    {
        $this->_disciplineManager = $disciplineManager;
        $this->_scheduleManager = $scheduleManager;
    }

    public function getAll()
    {
        try{
            $disciplines = $this->_disciplineManager->getAll();
            return $this->successJSONResponse($disciplines);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getByNameAndProfilePaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $profileId = $request->query('profile');
            $name = $request->query('name');

            $paginationResult = $this->_disciplineManager
                ->getByNameAndProfilePaginated($pageNum, $pageSize, $name, $profileId);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $disciplineData = $request->json('discipline');
            $profileIds = $request->json('profileIds');

            $discipline = new Discipline();
            $discipline->fillFromJson($disciplineData);
            $this->_disciplineManager->addDiscipline($discipline, $profileIds);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }

    }

    public function update(Request $request){
        try{
            $disciplineData = $request->json('discipline');
            $profileIds = $request->json('profileIds');

            $discipline = new Discipline();
            $discipline->fillFromJson($disciplineData);
            $this->_disciplineManager->updateDiscipline($discipline, $profileIds);

            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($id){
        try{
            $this->_disciplineManager->deleteDiscipline($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getThemes($id){
        try{
            $themes = $this->_disciplineManager->getDisciplineThemes($id);
            return $this->successJSONResponse($themes);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function createTheme(Request $request){
        try{
            $themeData = $request->json('theme');
            $disciplineId = $request->json('disciplineId');

            $theme = new Theme();
            $theme->fillFromJson($themeData);
            $this->_disciplineManager->addTheme($theme, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function updateTheme(Request $request){
        try{
            $themeData = $request->json('theme');
            $disciplineId = $request->json('disciplineId');

            $theme = new Theme();
            $theme->fillFromJson($themeData);
            $this->_disciplineManager->updateTheme($theme, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteTheme($id){
        try{
            $this->_disciplineManager->deleteTheme($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getDisciplineProfilesIds($id){
        try{
            $profileIds = $this->_disciplineManager->getDisciplineProfilesIds($id);
            return $this->successJSONResponse($profileIds);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getTestsByDiscipline($disciplineId){
        try{
            $tests = $this->_disciplineManager->getTestsByDiscipline($disciplineId);
            return $this->successJSONResponse($tests);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getTheme($id){
        try{
            $theme = $this->_disciplineManager->getTheme($id);
            return $this->successJSONResponse($theme);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getDiscipline($id){
        try{
            $discipline = $this->_disciplineManager->getDiscipline($id);
            return $this->successJSONResponse($discipline);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /*
     * Получение актуальных для пользователя дисциплин.
     * В списке будут дисциплины на текущий и предыдущие семестры обучения для студента.
     * Сортируются дисциплины по семестру, т.о. наверху списка всегда будут наиболее актуальные.
     * Номер семестра для текущего пользователя считается исходя из курса группы, в которой он находится.
     */
    public function getActualDisciplinesForStudent(){
        try{
            $currentUser = Auth::user();
            if (isset($currentUser)){
                $userId = $currentUser->getId();
                $currentSemester = $this->_scheduleManager->getCurrentSemesterForUser($userId);
                if (!isset($currentSemester) || $currentSemester == 0){

                    throw new Exception('Невозможно определить текущий семестр для пользователя');
                }
                $disciplines = $this->_disciplineManager->getActualDisciplinesForStudent($userId, $currentSemester);

                return $this->successJSONResponse($disciplines);
            } else {
                throw new Exception('Для данного действия необходимо авторизоваться!');
            }

        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
