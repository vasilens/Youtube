<?php

namespace router;

use exceptions\InvalidArgumentException;

class Router
{
    const REGEX = '/\d+/';
    const URI_DELIMITER = '/';
    const WILDCARD = '{id}';
    const URI_GLUE = '/';
    const CLASS_AND_METHOD_DELIMITER = '@';
    const CONTROLLER_NAME = '\\controller\\';
    const VIEW_ROUTER = 'view';
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
        $dynamicRoute = preg_match(self::REGEX, $this->uri);
        $arrayUri = explode(self::URI_DELIMITER, $this->uri);

        switch ($dynamicRoute) {
            case true:
                foreach ($arrayUri as $key => $value) {
                    if (is_numeric($value)) {
                        $arrayUri[$key] = self::WILDCARD;
                    }
                }
                $uri = implode(self::URI_GLUE, $arrayUri);
                if ($route === $uri) {
                    $classAndMethodArray = explode(self::CLASS_AND_METHOD_DELIMITER, $classAndMethod);
                    $className = self::CONTROLLER_NAME . ucfirst($classAndMethodArray[0]);
                    $method = $classAndMethodArray[1];
                    $controller = new $className;
                    $controller->$method();
                    die;
                }
                break;
            case false:
                if ($route === $this->uri) {
                    $classAndMethodArray = explode(self::CLASS_AND_METHOD_DELIMITER, $classAndMethod);
                    $className = self::CONTROLLER_NAME . ucfirst($classAndMethodArray[0]);
                    $method = $classAndMethodArray[1];
                    $controller = new $className;
                    $arrayUri[1] == self::VIEW_ROUTER ? $controller->$method($arrayUri[2]) : $controller->$method();
                    die;
                }
        }
    }
}