<?php


namespace router;


class Route
{
    /**
     * @var string
     */
    private $controller;
    /**
     * @var string
     */
    private $method;

    /**
     * Route constructor.
     * @param $controller
     * @param $method
     */
    public function __construct($controller, $method)
    {
        $this->controller = $controller;
        $this->method = $method;
    }

    public function execute()
    {
        if (class_exists($this->controller)) {
            if (method_exists($this->controller,$this->method)) {
                $this->controller->$this->method();
                die;
            }
        }
    }

    public function middleware()
    {

    }
}