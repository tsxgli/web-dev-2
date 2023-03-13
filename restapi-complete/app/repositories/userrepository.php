<?php

namespace Repositories;

use PDO;
use PDOException;
use Repositories\Repository;

class UserRepository extends Repository
{
    function checkUsernamePassword($username, $password)
    {
        try {
            // retrieve the user with the given username
            $stmt = $this->connection->prepare("SELECT id, username, password, email FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $user = $stmt->fetch();

            // verify if the password matches the hash in the database
            $result = $this->verifyPassword($password, $user->password);

            if (!$result)
                return false;

            // do not pass the password hash to the caller
            $user->password = "";

            return $user;
        } catch (PDOException $e) {
            echo $e;
        }
    }
    function registerUser(User $user)
    {
        try {
            $stmt = $this->connection->prepare('INSERT INTO User (firstname, lastname,email, password, isAdmin, address,postcode, birthdate) 
                                                    VALUES ( :firstname,:lastname, :email, :password, :isAdmin, :address, :postcode, :birthdate);');

            $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':address', $user->getAddress(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
            $stmt->bindValue(':postcode', $user->getPostCode(), PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $user->getLastName(), PDO::PARAM_STR);
            $stmt->bindValue(':firstname', $user->getFirstName(), PDO::PARAM_STR);
            $stmt->bindValue(':birthdate', $user->getBirthdate(), PDO::PARAM_STR);
            $stmt->bindValue(':isAdmin', $user->getIsAdmin(), PDO::PARAM_BOOL);

            $stmt->execute();

        } catch (PDOException $e) {
            echo "Registering user failed: " . $e->getMessage();
        }
    }
    // hash the password (currently uses bcrypt)
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // verify the password hash
    function verifyPassword($input, $hash)
    {
        return password_verify($input, $hash);
    }

    function getAll()
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM User");
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $users = $stmt->fetchAll();

            return $users;

        } catch (PDOException $e) {
            echo $e;
        }
    }
    function deleteUser($id)
    {
        try {
            $stmt = $this->connection->prepare("Delete from User where _id=:id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e;
        }
    }
    function updateUser($data)
    {

        try {
            $stmt = $this->connection->prepare("UPDATE User SET firstname = :firstname, lastname = :lastname, 
                                    postcode = :postcode, address = :address, 
                                    birthdate = :birthdate, email=:email,isAdmin = :isAdmin WHERE _id = :id");
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':firstname', $data['firstname']);
            $stmt->bindParam(':lastname', $data['lastname']);
            $stmt->bindParam(':postcode', $data['postcode']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':isAdmin', $data['isAdmin']);
            $stmt->execute();


        } catch (PDOException $e) {
            echo "Something went wrong updating the user: " . $e;
        }
    }
    function getUser($id)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM User WHERE _id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $row = $stmt->fetchAll();
            return $row;

        } catch (PDOException $e) {
            echo $e;
        }
    }
    function addUser($data)
    {
        try {
            $stmt = $this->connection->prepare('INSERT INTO User (firstname, lastname,email, password, isAdmin, address,postcode, birthdate) 
                                                    VALUES ( :firstname,:lastname, :email, :password, :isAdmin, :address, :postcode, :birthdate);');

            $stmt->bindParam(':firstname', $data['firstname']);
            $stmt->bindParam(':lastname', $data['lastname']);
            $stmt->bindParam(':postcode', $data['postcode']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':password', $data['password']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':isAdmin', $data['isAdmin']);
            $stmt->execute();

        } catch (PDOException $e) {
            echo "Adding user failed: " . $e->getMessage();
        }

    }

}