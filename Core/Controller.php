<?php

namespace API\Core;

/**
 * Created by PhpStorm.
 * User: cpolenz
 * Date: 27.06.17
 * Time: 12:45
 */
class Controller
{
    protected $errors;
    protected $params;
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        array_push($this->params, "session");
        $this->testParams($_COOKIE);
        $userModel = new \Models\Users();
        if(!$this->isValid()){
            $response = $this->response([], \API\Config\errors::$invalidSession);
            echo $response::get();
            exit();
        }
    }

    public function isValid(){
        $userId = $userModel->getUserIdBySession($this->params["session"]);
        if(!$userId){
            return false;
        }else{
            return true;
        }
    }

    public function index($id = -1)
    {
        $data = $this->loadFile($this->getPath());
        if ($id == -1) {
            return $this->response($data);
        } else {
            if (isset($data[$id])) {
                return $this->response($data[$id]);
            } else {
                return $this->response([],  \API\Config\errors::$invalidId);
            }
        }
    }

    public function testParams($params)
    {
        foreach ($this->params as $paramName) {
            if (isset($params[$paramName])) {
                $this->params[$paramName] = $params[$paramName];
            } elseif (!isset($this->params[$paramName])) {
                $this->params[$paramName] = "";
                $this->errors[$paramName] = "$paramName nicht gesetzt";
            }
        }
        if ($this->errors) {
            $this->response($this->errors,  \API\Config\errors::$missingParams);
        }
    }

    public function response($data = [], $error = "")
    {
        if ($error == "") {
            return new JsonResponse([
                "success" => true,
                "data"    => $data
            ]);
        } else {
            return new JsonResponse([
                "success" => false,
                "data"    => $data,
                "error"   => $error
            ]);
        }
    }
}