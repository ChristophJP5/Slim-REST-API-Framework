<?php

namespace Models;

use Core\Model;

class Users extends Model
{

    public function getUsers()
    {
        return $this->_db->fetchAll("SELECT * FROM users");
    }

    public function addUser($email, $name, $password, $created, $lastLogin, $session)
    {
        $this->_db->insert("users", array("name" => $name, "password" => $password, "created" => $created, "last_login" => $lastLogin, "session" => $session, "email" => $email));
    }

    public function getUserById($id)
    {
        return $this->_db->fetchRow("SELECT * FROM users WHERE id = :id", array(":id" => $id));
    }

    public function getUserIdByEmail($email)
    {
        return $this->_db->fetchOne("SELECT id FROM users WHERE email = :email", array(":email" => $email));
    }

    public function getUserBySession($session)
    {
        return $this->_db->fetchRow("SELECT * FROM users WHERE session = :session", array(":session" => $session));
    }

    public function getUserByEmail($email)
    {
        return $this->_db->fetchRow("SELECT * FROM users WHERE email = :email", array(":email" => $email));
    }

    public function getUserIdBySession($session)
    {
        return $this->_db->fetchOne("SELECT id FROM users WHERE session = :session", array(":session" => $session));
    }

    public function getUserByEmailAndPassword($email, $password)
    {
        return $this->_db->fetchRow("SELECT * FROM users WHERE `email` = :email AND password = :password", array(":email" => $email, ":password" => $password));
    }

    public function getUserNames()
    {
        return $this->_db->fetchAll("SELECT id, name FROM users");
    }

    public function setSessionAndLastLogin($userId, $session, $lastLogin)
    {
        $this->_db->update("users", array("session" => $session, "last_login" => $lastLogin), array("id" => $userId));
    }

}
