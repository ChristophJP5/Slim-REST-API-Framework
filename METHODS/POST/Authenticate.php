<?php

namespace API\METHODS\POST;

use API\Core\Controller;

class Authenticate extends Controller
{
    private $listeModel;
    private $usersModel;

    public function __construct()
    {
        $this->usersModel = new \Models\Users();
        $this->params     = ["name", "password", "email"];
        parent::__construct("login");
    }

    public function index($id = -1)
    {
        $name = $this->params["name"];
        $email = $this->params["email"];
        $password = $this->params["password"];

        $user = $this->usersModel->getUserEmail($email);
        if (!empty($user)) {
            if(password_verify($password, $user['password'])){
                $session = hash("sha256", time().microtime(true) . $email);
                $this->usersModel->setSessionAndLastLogin($user["id"], $session,time());
                $user["session"] = $session;
                setcookie("session",$session);
                unset($user['password']);
                return $this->response($user);
            }else {
                return $this->response([], "Userdaten sind falsch");
                // wrong password
            }
        } else {
            return $this->response([], "Userdaten sind falsch");
           // wrong email
        }
    }

    public function register(){
        
        $name = $this->params["name"];
        $email = $this->params["email"];
        $password = $this->params["password"];
        $password = password_hash($password,PASSWORD_BCRYPT,[
            'cost' => 12,
        ]);
        $this->usersModel->addUser($email, $name, $password, time(), time(), hash("sha256", time()));
        $this->index();
    }


    /*
     * Overwritte to ignore validation
    */
    public function isValid(){
        return true;
    }
}
