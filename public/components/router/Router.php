<?php

namespace router;

use components\router\http\Request;
use exceptions\InvalidArgumentException;

class Router
{
    const REGEX = '/\d+/';
    const URI_DELIMITER = '/';
    const WILDCARD = '{id}';
    const CLASS_AND_METHOD_DELIMITER = '@';
    const CONTROLLER_DIR = '\\controller\\';
    const VIEW_ROUTER = 'view';
    /**
     * @var array
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * @param string $route
     * @param string $classAndMethod
     *
     * @return mixed
     */
    public function route($route, $classAndMethod)
    {
        $reqUri = $this->request->getRequestUri();
        $dynamicRoute = preg_match(self::REGEX, $reqUri);
        $arrayUri = explode(self::URI_DELIMITER, $reqUri);

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
                    $controller->$method($this->request);
                    die;
                }
                break;
            case false:
                if ($route === $reqUri) {
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