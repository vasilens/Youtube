<?php

namespace components\router\http;

class Request
{
    /**
     * @var string
     */
    public $postParams;

    /**
     * @var string
     */
    public $getParams;

    /**
     * @var string
     */
    public $requestUri;

    /**
     * @var string
     */
    public $requestMethod;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getPostParams()
    {
        return $this->postParams;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
}
