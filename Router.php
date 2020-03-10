<?php

namespace router;

use exceptions\InvalidArgumentException;

class Router
{
    private $uri;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    public function route($route, $classAndMethod)
    {
        $flag = false;
        $intPlace = null;
        $arrayRoute = explode('/', $route);
        $arrayUri = explode('/', $this->uri);
//        echo "ROUTE";
//        var_dump($arrayRoute);
//        echo "URI";
//        var_dump($arrayUri);
        if ($this->uri == '/') {
            $classAndMethodArray = explode('@', $classAndMethod);
            $class = '\\controller\\' . ucfirst($classAndMethodArray[0]);
            $method = $classAndMethodArray[1];
            $controller = new $class;
            $controller->$method();
            die;
        }
        foreach ($arrayUri as $key => $value) {
            if (is_numeric($value)) {
                $flag = true;
                $intPlace = $key;
                break;
            }

        }
        if ($flag == false && $arrayUri === $arrayRoute && $arrayUri[1] == 'view') {
            $class = explode('@', $classAndMethod);
            $className = '\\controller\\' . ucfirst($class[0]);
            $method = $class[1];
            $controller = new $className;
//            var_dump($controller);
            $controller->$method($arrayUri[2]);
            die;
        }
        if ($flag == false && $arrayUri === $arrayRoute) {
            $class = explode('@', $classAndMethod);
            $className = '\\controller\\' . ucfirst($class[0]);
            $method = $class[1];
            $controller = new $className;
//            var_dump($controller);
//            var_dump($arrayUri);
            $controller->$method();
            die;
        }
        if ($flag == true) {
            $arrayRoute[$intPlace] = $arrayUri[$intPlace];
//            echo "URI";
//            var_dump($arrayUri);
//            echo "ROUTE";
//            var_dump($arrayRoute);
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