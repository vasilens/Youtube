<?php

namespace router\http;

class Request
{
    private $post;
    private $get;
    private $requestUri;
    private $requestMethod;
    private static $instance;

    private function __construct()
    {
    $this->get = $_GET;
    $this->post = $_POST;
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
        return $this->post;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->get;
    }

    /**
     * @return array
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
