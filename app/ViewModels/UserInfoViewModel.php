<?php


class UserInfoViewModel implements JsonSerializable
{
    private $id;

    private $firstName;

    private $middleName;

    private $lastName;

    private $email;

    private $active;

    private $role;

    private $group;

    /**
     * @param User $user
     */
    public function fillFromUser(User $user){
        $this->id = $user->getId();
        $this->firstName = $user->getFirstname();
        $this->middleName = $user->getPatronymic();
        $this->lastName = $user->getLastname();
        $this->active = $user->getActive();
        $this->email = $user->getEmail();
    }

    public function setRole($role){
        $this->role = $role;
    }

    public function getRole(){
        return $this->role;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstName,
            'patronymic' => $this->middleName,
            'lastname' => $this->lastName,
            'active' => $this->active,
            'email' => $this->email,
            'role' => $this->role,
            'group' => $this->group
        ];
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}