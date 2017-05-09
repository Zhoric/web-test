<?php
use Managers\UserManager;

class UserAccountTests extends TestCase
{
    /**
     * Получение заглушки класса репозитория пользователей.
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    private function getUserRepositoryStub(){
        //Заглушка класса менеджера сущностей.
        $entityManagerStub = $this->createMock(\Doctrine\ORM\EntityManager::class);

        //Заглушка класса репозитория пользователей.
        $userRepositoryStub = $this
            ->getMockBuilder(\Repositories\UserRepository::class)
            ->setConstructorArgs(array($entityManagerStub));

        return $userRepositoryStub;
    }

    /**
     * Получение заглушки класса логических транзакций уровня бизнес-модели (UnitOfWork).
     * @param $methodsNames - Наименование методов для получения указанных заглушек репозиториев.
     * @param $userRepositoryStubs - Заглушки репозиториев.
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getUnitOfWorkStub($methodsNames, $userRepositoryStubs){

        $unitOfWorkStub = $this->createMock(\Repositories\UnitOfWork::class);

        for ($i = 0; $i < count($methodsNames); $i++){
            $unitOfWorkStub
                ->method($methodsNames[$i])
                ->willReturn($userRepositoryStubs[$i]);
        }

        return $unitOfWorkStub;
    }

    /**
     * Добавление пользователя.
     */
    public function testAddUserShouldAddUser(){
        //Arrange
        $user = new User();
        $user->setEmail('testmail@mail.ru')
            ->setFirstname('Вася')
            ->setLastname('Пупкин')
            ->setActive(true);

        $userRepositoryStub = $this->getUserRepositoryStub()
            ->setMethods(['create'])
            ->getMock();

        //Условие успешности теста:
        $userRepositoryStub->expects($this->once())
            ->method('create')->with(
                $this->identicalTo($user)
            );

        $unitOfWorkStub = $this->getUnitOfWorkStub(['users'], [$userRepositoryStub]);

        $userManager = new UserManager($unitOfWorkStub);

        //Act & Assert
        $userManager->addUser($user);
    }

    public function testDeleteUserShouldSearchForUserAndDelete(){
        //Arrange
        $idOfUserToDelete = 1;
        $userStub = new User();
        $userStub->setFirstname('Иван');

        $userRepositoryStub = $this->getUserRepositoryStub()
            ->setMethods(['find','delete'])
            ->getMock();
        $userRepositoryStub->method('find')->willReturn($userStub);
        $userRepositoryStub->expects($this->once())
            ->method('find')->with($this->equalTo($idOfUserToDelete));
        $userRepositoryStub->expects($this->once())
            ->method('delete')->with($this->identicalTo($userStub));

        $unitOfWorkStub = $this->getUnitOfWorkStub(['users'], [$userRepositoryStub]);
        $userManager = new UserManager($unitOfWorkStub);

        //Act & Assert
        $userManager->deleteUser($idOfUserToDelete);
    }

    /**
     * Удаление пользователя должно выбрасывать исключение, если пользователь не найден.
     * @expectedException Exception
     * @expectedExceptionMessage пользователь не найден
     */
    public function testDeleteUserIfNotExistsShouldThrowException(){
        //Arrange
        $idOfUserToDelete = 1;

        $userRepositoryStub = $this->getUserRepositoryStub()
            ->setMethods(['find','delete'])
            ->getMock();
        $userRepositoryStub->method('find')->willReturn(null);
        $userRepositoryStub->expects($this->once())
            ->method('find')->with($this->equalTo($idOfUserToDelete));

        $unitOfWorkStub = $this->getUnitOfWorkStub(['users'], [$userRepositoryStub]);
        $userManager = new UserManager($unitOfWorkStub);

        //Act & Assert
        $userManager->deleteUser($idOfUserToDelete);
    }

    /**
     * Метод активации аккаунта (утверждения заявки на регистрацию) должен
     * устанавливать флаг активности пользователя в true.
     */
    public function testActivateShouldActivateUserIfExists(){
        //Arrange
        $idOfUserToActivate = 1;

        $userStub = new User();
        $userStub->setFirstname('Иван')
            ->setActive(false);

        $userRepositoryStub = $this->getUserRepositoryStub()
            ->setMethods(['find'])
            ->getMock();
        $userRepositoryStub->method('find')->willReturn($userStub);
        $userRepositoryStub->expects($this->once())
            ->method('find')->with($this->equalTo($idOfUserToActivate));


        $unitOfWorkStub = $this->getUnitOfWorkStub(['users'], [$userRepositoryStub]);
        $userManager = new UserManager($unitOfWorkStub);

        //Act
        $userManager->activate($idOfUserToActivate);

        //Assert
        $this->assertTrue($userStub->getActive());
    }

    /**
     * Метод активации аккаунта должен выбрасывать исключение, если аккаунт не найден.
     * @expectedException Exception
     * @expectedExceptionMessage Пользователь не найден
     */
    public function testActivateShouldThrowExceptionIfNotExists(){
        //Arrange
        $idOfUserToActivate = 1;

        $userRepositoryStub = $this->getUserRepositoryStub()
            ->setMethods(['find'])
            ->getMock();
        $userRepositoryStub->method('find')->willReturn(null);
        $userRepositoryStub->expects($this->once())
            ->method('find')->with($this->equalTo($idOfUserToActivate));


        $unitOfWorkStub = $this->getUnitOfWorkStub(['users'], [$userRepositoryStub]);
        $userManager = new UserManager($unitOfWorkStub);

        //Act & Assert
        $userManager->activate($idOfUserToActivate);
    }

}
