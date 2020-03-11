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
     * @param string $route
     * @param string $classAndMethod
     *
     * @return mixed
     */
    public function route($route, $classAndMethod)
    {
        $dynamicRoute = preg_match('/\d+/', $this->uri);
        $intPlace = null;
        $arrayRoute = explode('/', $route);
        $arrayUri = explode('/', $this->uri);
        switch ($dynamicRoute) {
            case 1:
                foreach ($arrayUri as $key => $value) {
                    if (is_numeric($value)) {
                        $intPlace = $key;
                        break;
                    }
                }
                $arrayRoute[$intPlace] = $arrayUri[$intPlace];
                if ($arrayRoute === $arrayUri) {
                    $class = explode('@', $classAndMethod);
                    $className = '\\controller\\' . ucfirst($class[0]);
                    $method = $class[1];
                    $controller = new $className;
                    $controller->$method();
                    die;
                }
                break;
            case 0:
                if ($arrayRoute === $arrayUri) {
                    $classAndMethodArray = explode('@', $classAndMethod);
                    $className = '\\controller\\' . ucfirst($classAndMethodArray[0]);
                    $method = $classAndMethodArray[1];
                    $controller = new $className;
                    $arrayUri[1] == 'view' ? $controller->$method($arrayUri[2]) : $controller->$method();
                    die;
                }
        }
    }
}