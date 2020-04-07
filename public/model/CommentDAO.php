<?php

namespace model;

class CommentDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'comments';
    }
}