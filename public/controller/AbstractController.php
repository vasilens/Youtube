<?php


namespace controller;


use components\router\http\Request;

abstract class AbstractController
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }
}
