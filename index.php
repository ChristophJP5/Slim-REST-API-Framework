<?php

$origin = isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'*';
header('Content-type:application/json');
header('Access-Control-Allow-Headers:content-type,session');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET,POST,DELETE,PUT,OPTIONS');
header('Access-Control-Allow-Origin:'.$origin);
// fastest way to handle a preflight not a beautifully way and shouldn't be used inproduction
// can also be handled in creating all needed endpoints in the /METHOODS/OPTIONS/ folder  
$requestMethod = $_SERVER['REQUEST_METHOD'];
if($requestMethod == 'OPTIONS') {
	header('HTTP/1.1 200 OK');
	exit;
}

// intense error reporting 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// mapped by .htaccess
$controller = ucfirst($_GET["controller"]);
$method     = $_GET["method"];


// require all needed files
loadFile("Config/Errors.php");
loadFile("Config/Config.php");
loadFiles("Core/Config");
loadFiles("Core");
loadFiles("Models");
// breakdown of requestet file to load the endpoint
loadFile("METHODS".DIRECTORY_SEPARATOR.$requestMethod.DIRECTORY_SEPARATOR.$controller.".php");

// load requested class and serve the request 
$className = "\\API\\METHODS\\".$requestMethod."\\".$controller;
if (class_exists($className)) {
    $object = new $className;
    // check if a custom method is called or just the index function
    if (method_exists($object, $method)) {
        $response = $object->{$method}($_GET["id"]);
        // all valid custom methodes must return a JsonResponse Object
        if (!$response instanceof \API\Core\JsonResponse) {
            // overwite response with an empty one 
            $response = new \API\Core\JsonResponse([
                "success" => false, 
                "data" => [], 
                "error" =>  \API\Config\errors::$emptyResponse
                ]);
        }
    } else {
        $response = new \API\Core\JsonResponse(["success" => false, "data" => [],
            "error" =>  \API\Config\errors::$missingMethod]);
    }
} else {
    $response = new \API\Core\JsonResponse(["success" => false, "data" => [], "error" =>  \API\Config\errors::$missingEndpoint]);
}

// handles files from an folder
function loadFiles($path)
{
    $core = glob($path.DIRECTORY_SEPARATOR."*.php");
    foreach ($core as $coreFile) {
        loadFile($coreFile);
    }
}

// load files
function loadFile($file)
{
    if (file_exists($file)) {
        require_once $file;
    }
}

// display json response
exit($response::get());
