<?php

namespace router;

use exceptions\InvalidArgumentException;
use components\router\http\Request;

class Router
{
    const REGEX = '/\d+/';
    const URI_DELIMITER = '/';
    const WILDCARD = '{id}';
    const CLASS_AND_METHOD_DELIMITER = '@';
    const CONTROLLER_DIR = '\\controller\\';
    const VIEW_ROUTER = 'view';
    /**
     * @var string
     */
//    private $uri;

//    public function __construct()
//    {
//        $this->uri = $_SERVER['REQUEST_URI'];
//    }

    /**
     * @param string $route
     * @param string $classAndMethod
     *
     * @return mixed
     */
    public function route($route, $classAndMethod)
    {
        $requestUri = Request::getInstance()->getRequestUri();
        var_dump($requestUri);
        $dynamicRoute = preg_match(self::REGEX, $requestUri);
        $arrayUri = explode(self::URI_DELIMITER, $requestUri);

        switch ($dynamicRoute) {
            case true:
                foreach ($arrayUri as $key => $value) {
                    if (is_numeric($value)) {
                        $arrayUri[$key] = self::WILDCARD;
                    }
                }
                $uri = implode(self::URI_DELIMITER, $arrayUri);
                if ($route === $uri) {
                    $classAndMethodArray = explode(self::CLASS_AND_METHOD_DELIMITER, $classAndMethod);
                    $className = self::CONTROLLER_DIR . ucfirst($classAndMethodArray[0]);
                    $method = $classAndMethodArray[1];
                    $controller = new $className;
                    $controller->$method();
                    die;
                }
                break;
            case false:
                if ($route === $requestUri) {
                    $classAndMethodArray = explode(self::CLASS_AND_METHOD_DELIMITER, $classAndMethod);
                    $className = self::CONTROLLER_DIR . ucfirst($classAndMethodArray[0]);
                    $method = $classAndMethodArray[1];
                    $controller = new $className;
                    $arrayUri[1] == self::VIEW_ROUTER ? $controller->$method($arrayUri[2]) : $controller->$method();
                    die;
                }
        }
    }
}