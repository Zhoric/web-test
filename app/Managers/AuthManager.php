<?php

namespace Managers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Repositories\UnitOfWork;
use RoleUser;
use User;
use UserRole;

class AuthManager
{
    protected $unitOfWork;
    protected $groupManager;

    public function __construct(UnitOfWork $unitOfWork, GroupManager $groupManager)
    {
        $this->unitOfWork = $unitOfWork;
        $this->groupManager = $groupManager;

    }

    public function checkIfEmailExists($email){
        if(!empty($this->unitOfWork->users()->findByEmail($email))){
            return true;
        }
        return false;
    }

    /**
     * Отправляет заявку на регистрацуию
     * @param User $user. Объект, содержащий креды
     * @param $groupId - айди группы, которой будет принадлежать зареганый юзер
     * @param $role  - роль зареганого юзера
     * @throws Exception
     * @return null|object
     */
    public function sendRegistrationRequest(User $user, $groupId,$role)
    {
            // По умолчанию регистрируем пользователя с ролью студента.
            if (!isset($role)){
                $role = UserRole::Student;
            }
            //Регистрируем его как неактивного
            $createdUser = $this->createNewUser($user, $role, false);

            if(isset($createdUser)) {
                $this->groupManager->setStudentGroup($groupId, $createdUser->getId());
            }
            else {
                throw new Exception('Ошибка при отправке заявки на регистрацию пользователя.');
            }

            return $createdUser;
    }

    /**
     * Создание нового пользователя с присвоением ему заданной роли.
     * В случае, если подаётся заявка на регистрацию, необходимо создать
     * неактивную учётную запись, передав значение false в параметр isActive.
     * @param User $user - данны пользователя.
     * @param $roleSlug - псевдоним присваиваемой пользователю роли.
     * @param bool $isActive - признак активности пользователя.
     * @return null|object - объект с данными о текущем пользователе.
     * @throws \Exception
     */
    public function createNewUser($user, $roleSlug, $isActive = true){
        $this->validateUserCreationPermissions($roleSlug, $isActive);

        $user->setActive($isActive);

        $role = $this->unitOfWork->roles()->getBySlug($roleSlug);
        if (!isset($role)){
            throw new Exception('Невозможно создать пользователя с указанной ролью. Роль не распознана!');
        }

        if($this->checkIfEmailExists($user->getEmail())){
           throw new Exception('Пользователь с данным email уже существует!');
        }

        $user->setPassword(bcrypt($user->getPassword()));
        $this->unitOfWork->users()->create($user);
        $this->unitOfWork->commit();
        $createdUser = $this->unitOfWork->users()->findByEmail($user->getEmail());

        $roleUser = new RoleUser();
        $roleUser->setRole($role);
        $roleUser->setUser($createdUser);
        $this->unitOfWork->userRoles()->create($roleUser);
        $this->unitOfWork->commit();

        return $createdUser;
    }



    public function checkIfUserActive($email){

        $user = $this->unitOfWork->users()->findByEmail($email);
        if($user == null){
            return false;
        }
        if($user->getActive()){
            return true;
        }
        return false;
    }

    /**
     * Проверка прав текущего пользователя на создание пользователей
     * с заданной ролью и активностью.
     * Например, студент не может создать учётную запись с другой ролью,
     * а также не может создать активную учётную запись. Активировать
     * учётную запись может только администратор после проверки заявки на регистрацию.
     * @param $roleSlug - Псевдоним роли, которую пытаемся присвоить пользовател.
     * @param $active - Признак активности пользователя.
     * @throws Exception
     */
    private function validateUserCreationPermissions($roleSlug, $active){

        $currentUser = Auth::user();
        $currentUserRole = 'none';
        $errorMessage = 'У вас недостаточно прав для выполнения данной операции!';

        //Если текущий пользователь авторизован, получаем его роль.
        if (isset($currentUser)){
            $currentUserRole = $this->unitOfWork->userRoles()->getRoleByUser($currentUser->getId());
            $currentUserRole = isset($currentUserRole) ? $currentUserRole->getSlug() : 'none';
        }

        //Если НЕ администратор пытается создать кого-либо, кроме студента.
        if ($currentUserRole != UserRole::Admin){
            if ($roleSlug != UserRole::Student){
                throw new Exception($errorMessage);
            }
        }

        //Если НЕ администратор или преподаватель пытается создать активную учётную запись.
        if ($currentUserRole != UserRole::Admin && $currentUserRole != UserRole::Lecturer){
            if ($active == true){
                throw new Exception($errorMessage);
            }
        }

    }

}