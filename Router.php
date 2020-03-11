<?php

namespace router;

use exceptions\InvalidArgumentException;

class Router
{
    /**
     * @var string
     */
    private $uri;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @param $route
     * @param $classAndMethod
     */
    public function route($route, $classAndMethod)
    {
        $flag = false;
        $intPlace = null;
        $arrayRoute = explode('/', $route);
        $arrayUri = explode('/', $this->uri);
        if (preg_match_all('/\d+/', $this->uri))
        {
            foreach ($arrayUri as $key => $value) {
                if (is_numeric($value)) {
                    $flag = true;
                    $intPlace = $key;
                    break;
                }
            }
        }
        if ($flag == false && $arrayUri === $arrayRoute) {
            $classAndMethodArray = explode('@', $classAndMethod);
            $className = '\\controller\\' . ucfirst($classAndMethodArray[0]);
            $method = $classAndMethodArray[1];
            $controller = new $className;
            $arrayUri[1] == 'view' ?   $controller->$method($arrayUri[2]) :  $controller->$method();
            die;
        }
        if ($flag == true) {
            $arrayRoute[$intPlace] = $arrayUri[$intPlace];
            if ($arrayRoute === $arrayUri) {
                $class = explode('@', $classAndMethod);
                $className = '\\controller\\' . ucfirst($class[0]);
                $method = $class[1];
                $controller = new $className;
                $controller->$method();
                die;
            }
        }
    }
}