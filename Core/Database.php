<?php

namespace Core;

use \PDO;

/*
 * database Helper - extending PDO to use custom methods
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */

class Database extends PDO
{

    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instances = array();

    public $filter = "";
//    public $itemsPage = "";
//    public $itemsToShow = "";


    /**
     * Static method get
     *
     * @param  array $group
     * @return \helpers\database
     */
    public static function get($group = false)
    {
        // Determining if exists or it's not empty, then use default group defined in config
        $group = !$group ? array(
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS
        ) : $group;

        // Group information
        $type = $group['type'];
        $host = $group['host'];
        $name = $group['name'];
        $user = $group['user'];
        $pass = $group['pass'];


        // ID for database based on the group information
        $id = "$type.$host.$name.$user.$pass";

//        self::$filter = $filterParent;
        // Checking if the same
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }


        try {
            // I've run into problem where
            // SET NAMES "UTF8" not working on some hostings.
            // Specifiying charset in DSN fixes the charset problem perfectly!
            $instance = new Database("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Setting Database into $instances to avoid duplication
            self::$instances[$id] = $instance;

            return $instance;
        } catch (PDOException $e) {
            //in the event of an error record the error to errorlog.html
//            Logger::newMessage($e);
//            Logger::customErrorMsg();
        }
    }

    /**
     * method for selecting records from a database
     * @param  string $sql sql query
     * @param  array $array named params
     * @param  object $fetchMode
     * @return array            returns an array of records
     */

    public function fetch($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->select($sql, $array, $fetchMode);
    }

    public function fetchAll($sql, $array = array())
    {
        return $this->select($sql, $array, "all");
    }

    public function fetchRow($sql, $array = array())
    {
        return $this->select($sql, $array, "row");
    }

    public function fetchColumn($sql, $array = array())
    {
        return $this->select($sql, $array, PDO::FETCH_COLUMN);
    }

    public function fetchOne($sql, $array = array())
    {
        return $this->select($sql, $array, "one");
    }

    public function select($sql, $selects, $fetchMode)
    {
        $stmt = $this->prepare($sql);
        foreach ($selects as $key => $value) {
            $fKey = str_replace("`", "", str_replace(array(".", "-"), "_", $key));
            if (substr($key, 0, 1) != ":") {
                $fKey = ":$fKey";
                if (!strpos($fKey, "id")) {
                    $value = "%" . $value . "%";
                }
            }
            if (is_int($value)) {
                $stmt->bindValue("$fKey", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$fKey", $value);
            }
        }

        $stmt->execute();
        switch ($fetchMode) {
            case "all":
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case "row":
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case "one":
                $result = $stmt->fetch(PDO::FETCH_COLUMN);
                break;
            default:
                $result = $stmt->fetchAll($fetchMode);
                break;
        }
        return $result;
    }

    /**
     * insert method
     * @param  string $table table name
     * @param  array $data array of columns and values
     * @param  boolean $ignore
     */
    public
    function insert($table, $data, $type = "default")
    {

        ksort($data);

        $fieldNames = "`" . implode('`,`', array_keys($data)) . "`";
        $fieldValues = ':' . implode(', :', array_keys($data));


        if ($type == "ignore") {
            // if ignore into is needed
            $stmt = $this->prepare("INSERT IGNORE INTO $table ($fieldNames) VALUES ($fieldValues)");
        } elseif ($type == "replace") {
            // if replace into is needed
            $stmt = $this->prepare("REPLACE INTO $table ($fieldNames) VALUES ($fieldValues)");
        } else {
            $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");
        }
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $this->lastInsertId();
    }

    /**
     * update method
     * @param  string $table table name
     * @param  array $data array of columns and values
     * @param  array $where array of columns and values
     */
    public
    function update($table, $data, $where)
    {

        ksort($data);

        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key` = :$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $whereDetails = NULL;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "`$key` = :where_$key";
            } else {
                $whereDetails .= " AND $key = :where_$key";
            }

            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');


        $sql = "UPDATE $table SET $fieldDetails WHERE $whereDetails";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        $stmt->execute();
        return $stmt->rowCount();

    }

    public
    function insertPrepared($sql, $array)
    {
        $stmt = $this->prepare($sql);
        foreach ($array as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $this->lastInsertId();
    }

    /**
     * Delete method
     * @param  string $table table name
     * @param  array $data array of columns and values
     * @param  array $where array of columns and values
     * @param  integer $limit limit number of records
     */
    public
    function delete($table, $where, $limit = 1)
    {

        ksort($where);

        $whereDetails = NULL;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }

            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        //if limit is a number use a limit on the query
        if (is_numeric($limit)) {
            $uselimit = "LIMIT $limit";
        }

        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");

        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * truncate table
     * @param  string $table table name
     */
    public
    function truncate($table)
    {
        return $this->exec("TRUNCATE TABLE $table");
    }

}
