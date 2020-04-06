<?php

namespace model;

class CategoryDAO extends AbstractDAO
{

    protected function setTable()
    {
        $this->table = 'categories';
    }
}