<?php

namespace model;

class CommentDAO extends AbstractDAO
{
    protected function setTable()
    {
        $this->table = 'comments';
    }
}