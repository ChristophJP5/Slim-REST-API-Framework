<?php

namespace API\Core;

class JsonResponse {
    protected static $data;
    function __construct($data = [])
    {
        self::$data = $data;
    }

    public static function get()
    {
        return json_encode(self::$data);
    }

    public static function set($data)
    {
        self::$data = $data;
    }
}
