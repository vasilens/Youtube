<?php

spl_autoload_register(function ($class){
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    require_once  $class;
});
session_start();
//echo $_SERVER['REQUEST_URI'];
//$uri = $_SERVER['REQUEST_URI'];
require_once('Router.php');
require_once('app/Routes.php');
use exceptions\BaseException;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo $_SERVER['REQUEST_URI'];
function handleExceptions(Exception $exception){
    $status = $exception instanceof BaseException ? $exception->getStatusCode() : 500;
    $msg = $exception->getMessage();
    if ($status == 500){
        $msg = "Server error!";
    }
    header($_SERVER["SERVER_PROTOCOL"]." " . $status);
    $html = "<h3 style='color: red'>$msg</h3>";
    include_once "view/main.php";
    echo $html;
    /*$obj = new stdClass();
    $obj->msg = $exception->getMessage();
    $obj->status = $status;*/
    //echo json_encode($obj);
}
set_exception_handler("handleExceptions");
?>