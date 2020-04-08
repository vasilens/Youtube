<?php

namespace services;

abstract class AbstractService
{

    /**
     * @var string
     */
    protected $dao;

    /**
     * AbstractService constructor.
     */
    public function __construct()
    {
        $this->setDao();

    }

    abstract protected function setDao();
}