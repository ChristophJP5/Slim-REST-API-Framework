<?php
/**
 * Created by PhpStorm.
 * User: cpolenz
 * Date: 13.03.17
 * Time: 16:11
 */

namespace Core;

abstract class Model
{

    /**
     * hold the database connection
     * @var object
     */
    protected $_db;

    /**
     * create a new instance of the database helper
     */
    public function __construct()
    {

        //connect to PDO here.
        $this->_db = \Core\database::get();
    }
}