<?php

namespace model;

class CategoryDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'categories';
    }
}