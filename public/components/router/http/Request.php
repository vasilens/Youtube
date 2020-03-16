<?php

namespace components\router\http;

class Request
{
    private $postParams;
    private $getParams;
    private $requestUri;
    private $requestMethod;
    private static $instance;

    private function __construct()
    {
    $this->getParams = $_GET;
    $this->postParams = $_POST;
    $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return Request
     */
    public static function getInstance()
    {
        if(self::$instance == null){
            self::$instance = new Request();
        }
        return self::$instance;
    }
    /**
     * @return array
     */
    public function postParams()
    {
        return $this->postParams;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->getParams;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @return array
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
}
