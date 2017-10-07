<?php

namespace API\METHODS\OPTIONS;

use API\Core\Controller;

class Authenticate extends Controller
{
    public function __construct()
    {
        $this->params     = [];
        parent::__construct("login");
    }

    public function index($id = -1)
    {
       return $this->response();
    }

    /*
     * Overwritte to ignore validation
    */
    public function isValid(){
        return true;
    }
}