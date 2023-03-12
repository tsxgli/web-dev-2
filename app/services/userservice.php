<?php
namespace Services;

use Repositories\UserRepository;

class UserService {

    private $repository;

    function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function checkUsernamePassword($username, $password) {
        return $this->repository->checkUsernamePassword($username, $password);
    }
    function getAll()
    {
        return $this->repository->getAll();
    }

    function deleteUser($id)
    {
        return $this->repository->deleteUser($id);
    }
    function updateUser($data)
    {
        $this->repository->updateUser($data);
    }
    function getUser($id)
    {
        return $this->repository->getUser($id);
    }
    function addUser($data)
    {
        return $this->repository->addUser($data);
    }
    public function registerUser(User $user) {
        $this->repository->registerUser($user);
      
    }
}

?>