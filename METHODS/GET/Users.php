<?php
/**
 * Created by PhpStorm.
 * User: cpolenz
 * Date: 27.06.17
 * Time: 12:54
 */

namespace API\METHODS\GET;

use API\Core\Controller;

class Users extends Controller
{
    public $usersModel;

    public function __construct()
    {
        $this->usersModel = new \Models\Users();
        $this->params     = ["name"];
        parent::__construct("lists");
    }

    public function index($id = -1)
    {
        $user = $this->usersModel->getUserIdBySession($this->params["session"]);
        unset($user['password']);
        return $this->response($user);
    }
}