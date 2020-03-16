<?php

namespace router\http;

class Request
{
    /**
     * @var array
     */
    private $postParams;

    /**
     * @var array
     */
    private $getParams;

    /**
     * @var string
     */
    private $requestUri;

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * Request constructor.
     */
    private function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return array
     */
    public function getPostParams()
    {
        return $this->postParams;
    }

    /**
     * @return array
     */
    public function getGetParams()
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
