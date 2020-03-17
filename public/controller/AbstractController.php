<?php

namespace controller;

use components\router\http\Request;

abstract class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * AbstractController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
